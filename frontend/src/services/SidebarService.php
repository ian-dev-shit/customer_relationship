<?php

namespace App\Services;

// 1. IMPORT API HELPER
require_once __DIR__ . '/../helpers/api_helper.php';

class SidebarService
{
    private array $session;

    public function __construct()
    {
        // Kukunin ang session data kapag in-instantiate ang class
        $this->session = $_SESSION ?? [];
    }

    /**
     * Pangunahing method para kunin lahat ng kailangan ng Sidebar.
     */
    public function getSidebarData(): array
    {
        $firstName = $this->session['first_name'] ?? '';
        $lastName  = $this->session['last_name'] ?? '';
        $userRole  = $this->session['role'] ?? 'sales_agent';
        $agentId   = $this->session['agent_id'] ?? 'SA-014';

        $apiStats   = $this->fetchApiStats();
        $navigation = $this->buildNavigation($userRole, $apiStats['leads'], $apiStats['alerts']);

        return [
            'userRole'    => $userRole,
            'agentId'     => $agentId,
            'displayName' => $this->formatDisplayName($firstName, $lastName),
            'initials'    => $this->formatInitials($firstName, $lastName),
            'activePage'  => $this->getActivePage(),
            'portalLabel' => $navigation['portalLabel'],
            'navSections' => $navigation['sections']
        ];
    }

    /**
     * Private Method: Pag-fetch ng Data sa API
     */
    private function fetchApiStats(): array
    {
        $leads = 0;
        $alerts = 0;

        if (function_exists('make_api_request')) {
            $response = make_api_request('/api/v1/leads/stats', 'GET');
            $data = $response['data'] ?? [];
            
            $leads  = (int)($data['all'] ?? 0);
            $alerts = (int)($data['new_inquiry'] ?? 0);
        }

        return ['leads' => $leads, 'alerts' => $alerts];
    }

    /**
     * Private Method: Pag-format ng Display Name
     */
    private function formatDisplayName(string $firstName, string $lastName): string
    {
        if (!empty($firstName) && !empty($lastName)) {
            return strtoupper($firstName[0]) . '. ' . ucfirst($lastName);
        }
        return $this->session['user_name'] ?? 'Sales Agent';
    }

    /**
     * Private Method: Pag-format ng Initials
     */
    private function formatInitials(string $firstName, string $lastName): string
    {
        $initial1 = !empty($firstName) ? strtoupper($firstName[0]) : 'U';
        $initial2 = !empty($lastName) ? strtoupper($lastName[0]) : 'A';
        return $initial1 . $initial2;
    }

    /**
     * Private Method: Kunin ang Active Page
     */
    private function getActivePage(): string
    {
        global $activePage; // Kung sakaling na-define na ito sa labas
        if (!empty($activePage)) {
            return $activePage;
        }
        $currentScript = basename($_SERVER['SCRIPT_NAME'] ?? '', '.php');
        return !empty($currentScript) ? $currentScript : 'dashboard';
    }

    /**
     * Private Method: Pagbuo ng Navigation batay sa Role
     */
    private function buildNavigation(string $role, int $leads, int $alerts): array
    {
        if ($role === 'sales_agent') {
            return [
                'portalLabel' => 'SALES AGENT PORTAL',
                'sections' => [
                    'OVERVIEW' => [
                        'dashboard' => ['label' => 'Dashboard', 'icon' => 'fa-chart-pie', 'url' => 'dashboard.php'],
                    ],
                    'PIPELINE' => [
                        'leads'     => [
                            'label' => 'My Leads', 
                            'icon' => 'fa-users-line', 
                            'url' => 'my_leads.php', 
                            'badge' => (string)$leads, 
                            'badgeColor' => 'bg-purple-500/20 text-purple-400'
                        ],
                        'kanban'    => ['label' => 'Kanban Pipeline', 'icon' => 'fa-bars-staggered', 'url' => 'kanban.php'],
                        'ai-alerts' => [
                            'label' => 'AI Escalations', 
                            'icon' => 'fa-robot', 
                            'url' => 'ai-escalations.php', 
                            'badge' => (string)$alerts, 
                            'badgeColor' => 'bg-red-500/20 text-red-400'
                        ],
                    ],
                    'DEALS' => [
                        'quotes'    => ['label' => 'Quotes & Offer', 'icon' => 'fa-file-signature', 'url' => 'quotes.php'],
                        'contracts' => ['label' => 'Contracts', 'icon' => 'fa-file-contract', 'url' => 'contracts.php'],
                    ],
                    'ROOMS' => [
                        'chat'      => ['label' => 'Direct Chat', 'icon' => 'fa-comments', 'url' => 'chat.php'],
                        'settings'  => ['label' => 'Settings', 'icon' => 'fa-gear', 'url' => 'settings.php'],
                    ]
                ]
            ];
        }

        return [
            'portalLabel' => 'CUSTOMER PORTAL',
            'sections' => [
                'OVERVIEW' => [
                    'dashboard' => ['label' => 'Dashboard', 'icon' => 'fa-border-all', 'url' => 'dashboard.php'],
                ],
                'FREIGHT' => [
                    'shipments'      => ['label' => 'Shipments', 'icon' => 'fa-box', 'url' => 'shipments.php', 'badge' => '12', 'badgeColor' => 'bg-amber-500/20 text-amber-400'],
                    'tracking'       => ['label' => 'Live Tracking', 'icon' => 'fa-location-dot', 'url' => 'tracking.php'],
                    'sla-monitoring' => ['label' => 'SLA Monitoring', 'icon' => 'fa-clock-rotate-left', 'url' => 'sla-monitoring.php'],
                ],
                'RECORDS' => [
                    'documents' => ['label' => 'Documents', 'icon' => 'fa-file-lines', 'url' => 'documents.php'],
                    'invoices'  => ['label' => 'Invoices & Billing', 'icon' => 'fa-file-invoice-dollar', 'url' => 'invoices.php'],
                    'analytics' => ['label' => 'BI Analytics', 'icon' => 'fa-chart-column', 'url' => 'analytics.php'],
                ],
                'SUPPORT' => [
                    'tickets'  => ['label' => 'Support Tickets', 'icon' => 'fa-comments', 'url' => 'tickets.php', 'badge' => '2', 'badgeColor' => 'bg-amber-500/20 text-amber-400'],
                    'settings' => ['label' => 'Settings', 'icon' => 'fa-gear', 'url' => 'settings.php'],
                ],
            ]
        ];
    }
}