<?php
namespace App\Controllers;

use App\Helpers\Database;
use App\Helpers\Session;
use App\Helpers\Rbac;
use App\Controllers\Controller;

class AiController extends Controller {
    private string $apiKey;
    private string $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent';

    public function __construct() {
        $this->apiKey = getenv('GEMINI_API_KEY') ?: '';
    }

    public function chat(): void {
        $this->auth();
        if (!Rbac::minLevel(7)) {
            $this->withError('Accès non autorisé.');
            $this->redirect('/dashboard');
            return;
        }
        $this->view('ai/chat');
    }

    public function ask(): void {
        $this->auth();
        if (!Rbac::minLevel(7)) {
            $this->json(['error' => 'Accès non autorisé.'], 403);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $message = trim($input['message'] ?? '');
        $history = $input['history'] ?? [];

        if ($message === '') {
            $this->json(['error' => 'Message vide.'], 400);
            return;
        }

        if (empty($this->apiKey)) {
            $this->json(['error' => 'Clé API Gemini non configurée.'], 500);
            return;
        }

        $db = Database::getConnection();
        $context = $this->getDatabaseContext($db);
        $systemPrompt = $this->buildSystemPrompt($context);
        $reply = $this->callGemini($systemPrompt, $message, $history);

        $this->json(['reply' => $reply]);
    }

    private function getDatabaseContext(\PDO $db): array {
        $safeQuery = function (string $sql, bool $single = true) use ($db): mixed {
            try {
                $stmt = $db->query($sql);
                return $single ? $stmt->fetchColumn() : $stmt->fetchAll();
            } catch (\PDOException $e) {
                return $single ? 0 : [];
            }
        };

        $total = (int)$safeQuery("SELECT COUNT(*) FROM reports WHERE deleted_at IS NULL");
        $statusStats = $safeQuery("SELECT status, COUNT(*) as cnt FROM reports WHERE deleted_at IS NULL GROUP BY status ORDER BY cnt DESC", false);
        $priorityStats = $safeQuery("SELECT priority, COUNT(*) as cnt FROM reports WHERE deleted_at IS NULL GROUP BY priority ORDER BY FIELD(priority,'urgent','high','medium','low')", false);
        $recent = $safeQuery("SELECT r.tracking_code, r.title, r.status, r.priority, r.created_at, c.name as cat FROM reports r LEFT JOIN categories c ON r.category_id = c.id WHERE r.deleted_at IS NULL ORDER BY r.created_at DESC LIMIT 5", false);
        $today = (int)$safeQuery("SELECT COUNT(*) FROM reports WHERE DATE(created_at) = CURDATE() AND deleted_at IS NULL");
        $week = (int)$safeQuery("SELECT COUNT(*) FROM reports WHERE YEARWEEK(created_at) = YEARWEEK(CURDATE()) AND deleted_at IS NULL");
        $month = (int)$safeQuery("SELECT COUNT(*) FROM reports WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) AND deleted_at IS NULL");
        $pending = (int)$safeQuery("SELECT COUNT(*) FROM reports WHERE status IN ('submitted','acknowledged') AND deleted_at IS NULL");
        $inProgress = (int)$safeQuery("SELECT COUNT(*) FROM reports WHERE status IN ('assigned','in_progress') AND deleted_at IS NULL");
        $resolved = (int)$safeQuery("SELECT COUNT(*) FROM reports WHERE status IN ('resolved','closed') AND deleted_at IS NULL");
        $urgent = (int)$safeQuery("SELECT COUNT(*) FROM reports WHERE priority='urgent' AND deleted_at IS NULL");
        $avgTime = $safeQuery("SELECT ROUND(AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)),1) FROM reports WHERE status IN ('resolved','closed') AND deleted_at IS NULL");
        $users = (int)$safeQuery("SELECT COUNT(*) FROM users WHERE deleted_at IS NULL");
        $interventions = (int)$safeQuery("SELECT COUNT(*) FROM report_interventions");
        $orgs = (int)$safeQuery("SELECT COUNT(*) FROM organizations WHERE deleted_at IS NULL");
        $topCats = $safeQuery("SELECT c.name, COUNT(*) as cnt FROM reports r JOIN categories c ON r.category_id = c.id WHERE r.deleted_at IS NULL GROUP BY r.category_id ORDER BY cnt DESC LIMIT 5", false);
        $topDairas = $safeQuery("SELECT d.name, COUNT(*) as cnt FROM reports r JOIN dairas d ON r.daira_id = d.id WHERE r.deleted_at IS NULL GROUP BY r.daira_id ORDER BY cnt DESC LIMIT 5", false);

        return compact('total','statusStats','priorityStats','urgent','recent','today','week','month','pending','inProgress','resolved','avgTime','users','interventions','orgs','topCats','topDairas');
    }

    private function buildSystemPrompt(array $ctx): string {
        $ss = '';
        foreach ($ctx['statusStats'] as $r) $ss .= "  - {$r['status']}: {$r['cnt']}\n";
        $ps = '';
        foreach ($ctx['priorityStats'] as $r) $ps .= "  - {$r['priority']}: {$r['cnt']}\n";
        $rc = '';
        foreach ($ctx['recent'] as $r) $rc .= "  - [{$r['tracking_code']}] {$r['title']} | {$r['status']} | {$r['priority']} | {$r['cat']}\n";
        $tc = '';
        foreach ($ctx['topCats'] as $r) $tc .= "  - {$r['name']}: {$r['cnt']}\n";
        $td = '';
        foreach ($ctx['topDairas'] as $r) $td .= "  - {$r['name']}: {$r['cnt']}\n";

        return <<<EOS
Tu es l'assistant IA de la plateforme Balagh Alger (signalement citoyen, Wilaya d'Alger).
Réponds UNIQUEMENT en français, sois concis, précis et utile.
Utilise des emojis avec parcimonie. N'indique jamais que tu lis des "données fournies".

CONTEXTE PLATEFORME (temps réel) :
- Total signalements : {$ctx['total']} | Aujourd'hui : {$ctx['today']} | Semaine : {$ctx['week']} | Mois : {$ctx['month']}
- En attente : {$ctx['pending']} | En cours : {$ctx['inProgress']} | Résolus : {$ctx['resolved']}
- Urgents : {$ctx['urgent']} | Temps moyen résolution : {$ctx['avgTime']}h
- Utilisateurs : {$ctx['users']} | Organismes : {$ctx['orgs']} | Interventions : {$ctx['interventions']}

STATUTS :
{$ss}
PRIORITÉS :
{$ps}
DERNIERS SIGNALEMENTS :
{$rc}
TOP CATÉGORIES :
{$tc}
TOP DAÏRAS :
{$td}
EOS;
    }

    private function callGemini(string $systemPrompt, string $message, array $history = []): string {
        $contents = [];
        foreach ($history as $h) {
            $contents[] = ['role' => $h['role'], 'parts' => [['text' => $h['parts'][0]['text']]]];
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $message]]];

        $payload = json_encode([
            'system_instruction' => [
                'parts' => [['text' => $systemPrompt]]
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 1024,
                'topP' => 0.9,
            ],
            'safetySettings' => [
                ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_NONE'],
                ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_NONE'],
                ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'],
                ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE'],
            ]
        ]);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->apiUrl . '?key=' . $this->apiKey,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) return "Erreur réseau : {$error}";
        if ($httpCode === 429) {
            return "Le service IA est temporairement saturé (trop de requêtes). Veuillez réessayer dans quelques instants. Si le problème persiste, contactez l'administrateur.";
        }
        if ($httpCode !== 200) {
            $err = json_decode($response, true);
            return "Erreur Gemini ({$httpCode}) : " . ($err['error']['message'] ?? 'Réponse invalide');
        }

        $data = json_decode($response, true);
        return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Désolé, je n\'ai pas pu générer une réponse.';
    }
}
