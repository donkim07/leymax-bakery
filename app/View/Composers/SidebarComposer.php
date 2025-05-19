<?php

namespace App\View\Composers;

use Illuminate\View\View;

class SidebarComposer
{
    public function compose(View $view)
    {
        $currentBusiness = request()->segment(1) ?? 'bakery';
        $sidebarItems = $this->getSidebarItems($currentBusiness);

        $view->with('sidebarItems', $sidebarItems);
        $view->with('currentBusiness', ucfirst($currentBusiness));
    }

    private function getSidebarItems(string $business): array
    {
        $items = [
            'bakery' => [
                ['title' => 'Dashboard', 'route' => 'bakery.dashboard', 'icon' => 'home'],
                [
                    'title' => 'Store Hierarchy',
                    'icon' => 'building-office',
                    'items' => [
                        ['title' => 'Main Stores', 'route' => 'bakery.stores.main', 'icon' => 'building-storefront'],
                        ['title' => 'Sub-Stores', 'route' => 'bakery.stores.sub', 'icon' => 'building'],
                        ['title' => 'Store Map', 'route' => 'bakery.stores.map', 'icon' => 'map'],
                    ],
                ],
                [
                    'title' => 'Inventory / Items',
                    'icon' => 'cube',
                    'items' => [
                        ['title' => 'Items & Variants', 'route' => 'bakery.items', 'icon' => 'squares-2x2'],
                        ['title' => 'Categories & Groups', 'route' => 'bakery.categories', 'icon' => 'folder'],
                        ['title' => 'Stock Adjustments', 'route' => 'bakery.stock.adjustments', 'icon' => 'adjustments-horizontal'],
                        ['title' => 'Stock Movement', 'route' => 'bakery.stock.movement', 'icon' => 'arrows-right-left'],
                        ['title' => 'Transfers & Requests', 'route' => 'bakery.transfers', 'icon' => 'arrows-right-left'],
                        ['title' => 'Reorder Alerts', 'route' => 'bakery.alerts', 'icon' => 'exclamation-triangle'],
                    ],
                ],
                [
                    'title' => 'Manufacturing',
                    'icon' => 'beaker',
                    'items' => [
                        ['title' => 'Inventory Assembly', 'route' => 'bakery.manufacturing.assembly', 'icon' => 'puzzle-piece'],
                        ['title' => 'Manufacture', 'route' => 'bakery.manufacturing.process', 'icon' => 'cake'],
                        ['title' => 'Stock Adjustment', 'route' => 'bakery.manufacturing.adjustment', 'icon' => 'adjustments-horizontal'],
                        ['title' => 'Production Planning', 'route' => 'bakery.manufacturing.planning', 'icon' => 'calendar'],
                        ['title' => 'Waste Management', 'route' => 'bakery.manufacturing.waste', 'icon' => 'trash'],
                        ['title' => 'Efficiency Metrics', 'route' => 'bakery.manufacturing.metrics', 'icon' => 'chart-bar'],
                    ],
                ],
                [
                    'title' => 'Sales',
                    'icon' => 'shopping-cart',
                    'items' => [
                        ['title' => 'Sales Orders', 'route' => 'bakery.sales.orders', 'icon' => 'shopping-bag'],
                        ['title' => 'Cash Sales (POS)', 'route' => 'bakery.sales.pos', 'icon' => 'banknotes'],
                        ['title' => 'Invoices', 'route' => 'bakery.sales.invoices', 'icon' => 'document-text'],
                        ['title' => 'Paid Invoices', 'route' => 'bakery.sales.invoices.paid', 'icon' => 'check-circle'],
                        ['title' => 'Delivery & Fulfillment', 'route' => 'bakery.delivery', 'icon' => 'truck'],
                        ['title' => 'Returns & Refunds', 'route' => 'bakery.returns', 'icon' => 'arrow-path'],
                    ],
                ],
                [
                    'title' => 'Purchases',
                    'icon' => 'shopping-bag',
                    'items' => [
                        ['title' => 'Suppliers', 'route' => 'bakery.suppliers', 'icon' => 'users'],
                        ['title' => 'Purchase Orders', 'route' => 'bakery.purchases.orders', 'icon' => 'document'],
                        ['title' => 'Bills & Payments', 'route' => 'bakery.bills', 'icon' => 'receipt-percent'],
                        ['title' => 'Supplier Credits', 'route' => 'bakery.credits', 'icon' => 'credit-card'],
                        ['title' => 'Receive Items', 'route' => 'bakery.receive', 'icon' => 'inbox-arrow-down'],
                        ['title' => 'Purchase Returns', 'route' => 'bakery.purchase-returns', 'icon' => 'arrow-uturn-left'],
                    ],
                ],
                [
                    'title' => 'Accounting',
                    'icon' => 'calculator',
                    'items' => [
                        ['title' => 'Chart of Accounts', 'route' => 'bakery.accounts', 'icon' => 'document-chart-bar'],
                        ['title' => 'Expenses', 'route' => 'bakery.expenses', 'icon' => 'banknotes'],
                        ['title' => 'Journal Entries', 'route' => 'bakery.journal', 'icon' => 'document-text'],
                        ['title' => 'Banking', 'route' => 'bakery.banking', 'icon' => 'currency-dollar'],
                        ['title' => 'Payroll & HR', 'route' => 'bakery.payroll', 'icon' => 'user-group'],
                    ],
                ],
                ['title' => 'Reports', 'route' => 'bakery.reports', 'icon' => 'chart-bar'],
            ],
            'tools' => [
                ['title' => 'Dashboard', 'route' => 'tools.dashboard', 'icon' => 'home'],
                [
                    'title' => 'Store Hierarchy',
                    'icon' => 'building-office',
                    'items' => [
                        ['title' => 'Main Stores', 'route' => 'tools.stores.main', 'icon' => 'building-storefront'],
                        ['title' => 'Sub-Stores', 'route' => 'tools.stores.sub', 'icon' => 'building'],
                        ['title' => 'Store Map', 'route' => 'tools.stores.map', 'icon' => 'map'],
                    ],
                ],
                [
                    'title' => 'Inventory / Items',
                    'icon' => 'cube',
                    'items' => [
                        ['title' => 'Items & Variants', 'route' => 'tools.items', 'icon' => 'squares-2x2'],
                        ['title' => 'Categories & Groups', 'route' => 'tools.categories', 'icon' => 'folder'],
                        ['title' => 'Stock Adjustments', 'route' => 'tools.stock.adjustments', 'icon' => 'adjustments-horizontal'],
                        ['title' => 'Stock Movement', 'route' => 'tools.stock.movement', 'icon' => 'arrows-right-left'],
                        ['title' => 'Transfers & Requests', 'route' => 'tools.transfers', 'icon' => 'arrows-right-left'],
                        ['title' => 'Reorder Alerts', 'route' => 'tools.alerts', 'icon' => 'exclamation-triangle'],
                    ],
                ],
                [
                    'title' => 'Sales',
                    'icon' => 'shopping-cart',
                    'items' => [
                        ['title' => 'Sales Orders', 'route' => 'tools.sales.orders', 'icon' => 'shopping-bag'],
                        ['title' => 'Cash Sales (POS)', 'route' => 'tools.sales.pos', 'icon' => 'banknotes'],
                        ['title' => 'Invoices', 'route' => 'tools.invoices', 'icon' => 'document-text'],
                        ['title' => 'Paid Invoices', 'route' => 'tools.invoices.paid', 'icon' => 'check-circle'],
                        ['title' => 'Delivery & Fulfillment', 'route' => 'tools.delivery', 'icon' => 'truck'],
                        ['title' => 'Returns & Refunds', 'route' => 'tools.returns', 'icon' => 'arrow-path'],
                    ],
                ],
                [
                    'title' => 'Purchases',
                    'icon' => 'shopping-bag',
                    'items' => [
                        ['title' => 'Suppliers', 'route' => 'tools.suppliers', 'icon' => 'users'],
                        ['title' => 'Purchase Orders', 'route' => 'tools.purchases.orders', 'icon' => 'document'],
                        ['title' => 'Bills & Payments', 'route' => 'tools.bills', 'icon' => 'receipt-percent'],
                        ['title' => 'Supplier Credits', 'route' => 'tools.credits', 'icon' => 'credit-card'],
                        ['title' => 'Receive Items', 'route' => 'tools.receive', 'icon' => 'inbox-arrow-down'],
                        ['title' => 'Purchase Returns', 'route' => 'tools.purchase-returns', 'icon' => 'arrow-uturn-left'],
                    ],
                ],
                [
                    'title' => 'Accounting',
                    'icon' => 'calculator',
                    'items' => [
                        ['title' => 'Chart of Accounts', 'route' => 'tools.accounts', 'icon' => 'document-chart-bar'],
                        ['title' => 'Expenses', 'route' => 'tools.expenses', 'icon' => 'banknotes'],
                        ['title' => 'Journal Entries', 'route' => 'tools.journal', 'icon' => 'document-text'],
                        ['title' => 'Banking', 'route' => 'tools.banking', 'icon' => 'currency-dollar'],
                        ['title' => 'Payroll & HR', 'route' => 'tools.payroll', 'icon' => 'user-group'],
                    ],
                ],
                ['title' => 'Reports', 'route' => 'tools.reports', 'icon' => 'chart-bar'],
            ],
            'academy' => [
                ['title' => 'Dashboard', 'route' => 'academy.dashboard', 'icon' => 'home'],
                [
                    'title' => 'Courses',
                    'icon' => 'academic-cap',
                    'items' => [
                        ['title' => 'All Courses', 'route' => 'academy.courses', 'icon' => 'squares-2x2'],
                        ['title' => 'Categories', 'route' => 'academy.categories', 'icon' => 'folder'],
                        ['title' => 'Media Library', 'route' => 'academy.media', 'icon' => 'photo'],
                    ],
                ],
                [
                    'title' => 'Enrollments',
                    'icon' => 'users',
                    'items' => [
                        ['title' => 'Student List', 'route' => 'academy.students', 'icon' => 'user-group'],
                        ['title' => 'Progress Tracker', 'route' => 'academy.progress', 'icon' => 'chart-bar'],
                    ],
                ],
                [
                    'title' => 'Course-Item Links',
                    'icon' => 'link',
                    'items' => [
                        ['title' => 'Link Items to Courses', 'route' => 'academy.links', 'icon' => 'link'],
                    ],
                ],
                [
                    'title' => 'Payments',
                    'icon' => 'banknotes',
                    'items' => [
                        ['title' => 'Course Orders', 'route' => 'academy.orders', 'icon' => 'shopping-cart'],
                        ['title' => 'Invoices & Payments', 'route' => 'academy.invoices', 'icon' => 'document-text'],
                    ],
                ],
                ['title' => 'Reports', 'route' => 'academy.reports', 'icon' => 'chart-bar'],
            ],
            'default' => [
                ['title' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'home'],
                [
                    'title' => 'Quick Access',
                    'icon' => 'bolt',
                    'items' => [
                        ['title' => 'Bakery Shop', 'route' => 'bakery.dashboard', 'icon' => 'cake'],
                        ['title' => 'Cake Tools', 'route' => 'tools.dashboard', 'icon' => 'wrench'],
                        ['title' => 'Academy', 'route' => 'academy.dashboard', 'icon' => 'academic-cap'],
                    ],
                ],
                [
                    'title' => 'Global Reports',
                    'icon' => 'chart-bar',
                    'items' => [
                        ['title' => 'Sales Overview', 'route' => 'reports.sales', 'icon' => 'arrow-trending-up'],
                        ['title' => 'Inventory Status', 'route' => 'reports.inventory', 'icon' => 'cube'],
                        ['title' => 'Financial Summary', 'route' => 'reports.financial', 'icon' => 'currency-dollar'],
                    ],
                ],
                [
                    'title' => 'System',
                    'icon' => 'cog-6-tooth',
                    'items' => [
                        ['title' => 'General Settings', 'route' => 'settings.general', 'icon' => 'adjustments-horizontal'],
                        ['title' => 'User Management', 'route' => 'settings.users', 'icon' => 'users'],
                        ['title' => 'Roles & Permissions', 'route' => 'settings.roles', 'icon' => 'shield-check'],
                        ['title' => 'Backup & Restore', 'route' => 'settings.backup', 'icon' => 'server'],
                    ],
                ],
            ],
        ];

        return $items[$business] ?? $items['default'];
    }
} 