<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\MaturityAssessment;
use App\Repository\MaturityRepository;
use Exception;

class MaturityController extends BaseController
{
    private MaturityRepository $repository;
    private int $organizationId;
    private int $userId;

    public function __construct()
    {
        $this->ensureAuthenticated();
        $this->repository = new MaturityRepository();
        $this->organizationId = (int) $_SESSION['organization_id'];
        $this->userId = (int) $_SESSION['user_id'];
    }

    public function index(): void
    {
        $history = $this->repository->findAllByOrganizationId($this->organizationId);
        $latest = $history[0] ?? null;
        $previous = $history[1] ?? null;

        $recommendations = [];
        if ($latest) {
            $recommendations = $this->generateRecommendations($latest);
        }

        $this->render('maturity/index', [
            'title' => 'Auto-évaluation de Maturité',
            'latest' => $latest,
            'previous' => $previous,
            'history' => $history,
            'recommendations' => $recommendations
        ]);
    }

    private function generateRecommendations(MaturityAssessment $latest): array
    {
        $advice = [
            'governanceScore' => [
                'label' => 'Gouvernance',
                'text' => 'Désignez un DPO, impliquez la direction dans les décisions RGPD et sensibilisez régulièrement vos collaborateurs.'
            ],
            'registryScore' => [
                'label' => 'Registre',
                'text' => 'Recensez l\'ensemble de vos traitements. Assurez-vous que chaque finalité est associée à une base légale valide.'
            ],
            'rightsScore' => [
                'label' => 'Droits & Processus',
                'text' => 'Mettez en place des procédures claires pour répondre aux demandes d\'exercice de droits sous 30 jours.'
            ],
            'securityScore' => [
                'label' => 'Sécurité',
                'text' => 'Renforcez la sécurité de vos accès (MFA, mots de passe) et assurez-vous du chiffrement des données sensibles.'
            ],
            'riskScore' => [
                'label' => 'Risques & Tiers',
                'text' => 'Réalisez des AIPD pour vos traitements à risque et auditez la conformité de vos sous-traitants (Art. 28).'
            ]
        ];

        $recos = [];
        foreach ($advice as $property => $data) {
            if ($latest->$property < 3.0) {
                $recos[] = $data;
            }
        }
        return $recos;
    }

    public function assessment(): void
    {
        $this->render('maturity/form', [
            'title' => 'Lancer une auto-évaluation',
            'pillars' => $this->getPillars()
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();
        try {
            $scores = [];
            $pillars = $this->getPillars();

            foreach ($pillars as $key => $pillar) {
                $pillarTotal = 0;
                $count = count($pillar['questions']);
                foreach ($pillar['questions'] as $qIndex => $q) {
                    $scoreValue = (int) ($_POST["q_{$key}_{$qIndex}"] ?? 0);
                    $pillarTotal += $scoreValue;
                }
                // Score moyen sur 5 pour ce pilier
                $scores[$key] = ($count > 0) ? ($pillarTotal / $count) : 0;
            }

            $assessment = new MaturityAssessment(
                null,
                $this->organizationId,
                $this->userId,
                (float) $scores['governance'],
                (float) $scores['registry'],
                (float) $scores['rights'],
                (float) $scores['security'],
                (float) $scores['risk'],
                $_POST['comments'] ?? null
            );

            $this->repository->save($assessment);
            $this->auditLog('MATURITY_ASSESSMENT_CREATE', 'maturity_assessment');

            $_SESSION['flash_success'] = "Auto-évaluation enregistrée avec succès.";
            $this->redirect('index.php?page=maturity&action=index');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Erreur : " . $e->getMessage();
            $this->redirect('index.php?page=maturity&action=assessment');
        }
    }

    private function getPillars(): array
    {
        return [
            'governance' => [
                'label' => 'Gouvernance',
                'questions' => [
                    'Un pilote (DPO) a-t-il été désigné ?',
                    'La direction est-elle impliquée et informée ?',
                    'Des formations régulières sont-elles organisées ?'
                ]
            ],
            'registry' => [
                'label' => 'Registre & Traitements',
                'questions' => [
                    'Le registre des traitements est-il exhaustif et à jour ?',
                    'Tous les services contribuent-ils au recensement ?',
                    'Les finalités et bases légales sont-elles documentées ?'
                ]
            ],
            'rights' => [
                'label' => 'Droits & Processus',
                'questions' => [
                    'Un processus de gestion des droits (accès, etc.) est en place ?',
                    'L\'information aux personnes est-elle transparente ?',
                    'La gestion des violations de données est-elle documentées ?'
                ]
            ],
            'security' => [
                'label' => 'Sécurité technique',
                'questions' => [
                    'Une politique de sécurité informatique est appliquée ?',
                    'Les accès logiques et physiques sont-ils sécurisés ?',
                    'Les serveurs et sauvegardes sont-ils protégés ?'
                ]
            ],
            'risk' => [
                'label' => 'Gestion des Risques & Tiers',
                'questions' => [
                    'Des AIPD sont réalisées pour les traitements à risque ?',
                    'Les contrats sous-traitants sont-ils conformes (Art. 28) ?',
                    'La conformité est-elle auditée régulièrement ?'
                ]
            ]
        ];
    }
}
