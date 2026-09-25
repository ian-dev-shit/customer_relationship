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

        // Ipasa ang $userRole para tama ang API na tatawagin
        $apiStats   = $this->fetchApiStats($userRole);
        $navigation = $this->buildNavigation($userRole, $apiStats);

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
    private function fetchApiStats(string $role): array
    {
        $leads = 0;
        $alerts = 0;
        $tickets = 0;

        if (function_exists('make_api_request')) {
            if ($role === 'admin') {
                // Fetch Close-Won Tickets na 'for account' pa lang mula sa FastAPI
                $ticketRes = make_api_request('/api/v1/admin/close-won-tickets', 'GET');
                $ticketsData = $ticketRes['data'] ?? [];
                $tickets = count($ticketsData);
            } else {
                // Fetch Sales Agent Stats
                $response = make_api_request('/api/v1/leads/stats', 'GET');
                $data = $response['data'] ?? [];
                
                $leads  = (int)($data['all'] ?? 0);
                $alerts = (int)($data['new_inquiry'] ?? 0);
            }
        }

        return ['leads' => $leads, 'alerts' => $alerts, 'tickets' => $tickets];
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
    private function buildNavigation(string $role, array $stats): array
    {
        $leads   = $stats['leads'] ?? 0;
        $alerts  = $stats['alerts'] ?? 0;
        $tickets = $stats['tickets'] ?? 0;

        if ($role === 'admin') {
            return [
                'portalLabel' => 'ADMIN PORTAL',
                'sections' => [
                    'OVERVIEW' => [
                        'dashboard' => ['label' => 'Dashboard', 'icon' => 'fa-chart-pie', 'url' => 'dashboard.php'],
                    ],
                    'MANAGEMENT' => [
                        'tickets' => [
                            'label' => 'Account Tickets', 
                            'icon' => 'fa-ticket-simple', 
                            'url' => 'tickets.php',
                            'badge' => (string)$tickets,
                            'badgeColor' => $tickets > 0 ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-800 text-slate-500'
                        ],
                        // SUBMENU: Accounts & Users Management
                        'accounts_management' => [
                            'label' => 'User Management',
                            'icon' => 'fa-users-gear',
                            'submenu' => [
                                'agents'    => ['label' => 'Sales Agents', 'url' => 'agents.php'],
                            ]
                        ]
                    ],
                    'Reports' => [
                        // SUBMENU: BI Analytics
                        'bi_analytics' => [
                            'label' => 'BI Analytics',
                            'icon'  => 'fa-chart-line',
                            'submenu' => [
                                'descriptive' => ['label' => 'Descriptive Analytics', 'url' => 'descriptive_analytics.php'],
                                'diagnostic'  => ['label' => 'Diagnostic Analytics',  'url' => 'diagnostic_analytics.php'],
                                'predictive'  => ['label' => 'Predictive Analytics',  'url' => 'predictive_analytics.php'],
                                'prescriptive' => ['label' => 'Prescriptive Analytics', 'url' => 'prescriptive_analytics.php'],
                            ]
                        ],
                        // SUBMENU: Sales Reports
                        'sales_reports' => [
                            'label' => 'Sales Reports',
                            'icon'  => 'fa-file-invoice-dollar',
                            'submenu' => [
                                'sales_summary' => ['label' => 'Sales Summary', 'url' => 'sales_summary.php'],
                                'payments'      => ['label' => 'Payment History', 'url' => 'payments.php'],
                            ]
                        ]
                    ],
                ]
            ];
        }

        if ($role === 'super_admin') {
            return [
                'portalLabel' => 'SUPER ADMIN PORTAL',
                'sections' => [
                    'OVERVIEW' => [
                        'dashboard' => ['label' => 'Dashboard', 'icon' => 'fa-chart-pie', 'url' => 'dashboard.php'],
                    ],
                    'ACCOUNT MANAGEMENT' => [
                        'create_customer' => [
                            'label' => 'Create Customer Acc', 
                            'icon'  => 'fa-user-plus', 
                            'url'   => 'create_customer.php'
                        ],
                        'user_management' => [
                            'label'   => 'User Management',
                            'icon'    => 'fa-users-gear',
                            'submenu' => [
                                'customers' => ['label' => 'Customer Accounts', 'url' => 'customer.php'],
                            ]
                        ]
                    ],
                    'SECURITY & CONTROL' => [
                        'restrictions' => [
                            'label' => 'Restrictions & Access', 
                            'icon'  => 'fa-user-lock', 
                            'url'   => 'restriction.php'
                        ],
                        'audit_logs' => [
                            'label' => 'Audit Logs', 
                            'icon'  => 'fa-clipboard-list', 
                            'url'   => 'audit_logs.php'
                        ],
                    ],
                    
                ]
            ];
        }

        if ($role === 'sales_agent') {
            return [
                'portalLabel' => 'SALES AGENT PORTAL',
                'sections' => [
                    'OVERVIEW' => [
                        'dashboard' => ['label' => 'Dashboard', 'icon' => 'fa-chart-pie', 'url' => '/src/views/sales_agent/dashboard.php'],
                    ],
                    'PIPELINE' => [
                        'leads' => [
                            'label' => 'My Leads', 
                            'icon' => 'fa-users-line', 
                            'url' => '/src/views/sales_agent/my_leads.php', 
                            'badge' => (string)$leads, 
                            'badgeColor' => 'bg-purple-500/20 text-purple-400'
                        ],
                        'kanban' => ['label' => 'Sales Board', 
                                     'icon' => 'fa-columns', 
                                     'url' => '/src/views/sales_agent/kanban.php'],
                    ],
                    'ACCOUNTS & CLIENTS' => [
                        'accounts_group' => [
                            'label' => 'Account & Clients',
                            'icon' => 'fa-users-gear',
                            'submenu' => [
                                'customers' => ['label' => 'Customers', 'icon' => 'fa-users', 'url' => '/src/views/sales_agent/customer.php'],
                                'book_shipment' => ['label' => 'Book Shipment', 'icon' => 'fa-box-archive', 'url' => 'book_shipment.php'],
                                'shipment' => ['label' => 'Shipment Tracking', 'icon' => 'fa-truck-field', 'url' => 'shipment.php'],
                            ]
                        ]
                    ],
                    'DOCUMENTS' => [
                        'documents_group' => [
                            'label' => 'Documents Manager',
                            'icon' => 'fa-folder-open',
                            'submenu' => [
                                'documents' => ['label' => 'Documents', 'icon' => 'fa-file-lines', 'url' => 'document.php'],
                            ]
                        ]
                    ],
                    'DEALS' => [
                        // SUBMENU: Grouping Deals & Quotes together
                        'deals_group' => [
                            'label' => 'Deals & Offers',
                            'icon' => 'fa-briefcase',
                            'submenu' => [
                                'rate_search' => ['label' => 'Rate Search', 'icon' => 'fa-calculator', 'url' => 'rates_search.php'],
                                
                            ]
                        ]
                    ],
                    'REPORT' => [
                        'analytics' => ['label' => 'Analytics & Reports', 'icon' => 'fa-chart-line', 'url' => '/src/views/sales_agent/bi_analytics.php'],
                        'post_sales' => ['label' => 'Post-Sales & Events', 'icon' => 'fa-calendar-check', 'url' => '/src/views/sales_agent/post_event.php'],
                    ],
                    'ROOMS' => [
                        'chat'     => ['label' => 'Direct Chat', 'icon' => 'fa-comments', 'url' => '../chat/chat.php'],
                        'settings' => ['label' => 'Settings', 'icon' => 'fa-gear', 'url' => 'settings.php'],
                    ]
                ]
            ];
        }

       if ($role === 'customer') {
            return [
                'portalLabel' => 'CUSTOMER PORTAL',
                'sections' => [
                    'OVERVIEW' => [
                        'dashboard' => ['label' => 'Dashboard', 'icon' => 'fa-chart-pie', 'url' => '/src/views/customer/dashboard.php'],
                    ],
                    'SHIPMENTS' => [
                        'book_shipment' => ['label' => 'Book Shipment', 'icon' => 'fa-box-archive', 'url' => '/src/views/customer/book_shipment.php'],
                        'shipment_tracking' => ['label' => 'Shipment Tracking', 'icon' => 'fa-truck-field', 'url' => '/src/views/customer/shipment.php'],
                    ],
                    'DOCUMENTS' => [
                        'documents_group' => [
                            'label' => 'Documents Manager',
                            'icon'  => 'fa-folder-open',
                            'submenu' => [
                                'documents' => ['label' => 'Documents', 'icon'  => 'fa-file-lines',  'url'   => '/src/views/customer/document.php'],
                            ]
                        ]
                    ],
                    // Additional sections for customers can be added here
                ]
            ];
        }

        // Default fallback if role is unrecognized
        return [
            'portalLabel' => strtoupper($role) . " PORTAL",
            'sections'    => []
        ];
    }
}