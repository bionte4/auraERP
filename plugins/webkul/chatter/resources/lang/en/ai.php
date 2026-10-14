<?php

return [
    'widget' => [
        'button'      => 'auraAI',
        'title'       => 'auraAI Assistant (Chatter & ERP)',
        'reset'       => 'Reset',
        'placeholder' => 'Ask about attendance, contracts, performance, projects...',
        'send'        => 'Send',
        'greeting'    => "Hello! I am auraAI Assistant connected to your Chatter & ERP data.\nPick a quick topic above or type your question.",
        'presets'     => [
            'attendance'  => 'Today\'s attendance',
            'contracts'   => 'Expiring contracts',
            'performance' => 'Performance & evaluations',
            'employees'   => 'Employee overview',
        ],
    ],

    'chatter' => [
        'button'             => 'Ask AI',
        'modal-heading'      => 'auraAI Assistant — Record & Chatter Analysis',
        'modal-description'  => 'AI will summarize this record along with its Chatter history, or answer your prompt directly into the Chatter thread.',
        'prompt-label'       => 'Instruction or question (optional)',
        'prompt-placeholder' => 'Leave blank to generate an automatic summary of this record...',
        'submit'             => 'Post AI Reply to Chatter',
        'reply-subject'      => 'auraAI Summary & Analysis',
        'notification-title' => 'AI analysis posted',
        'notification-body'  => 'auraAI has posted a note in this record\'s Chatter thread.',
    ],

    'summary' => [
        'header'         => ':type Summary: :title',
        'recent-chatter' => 'Recent Chatter Messages/Notes:',
    ],

    'employee' => [
        'job'              => 'Job Title: :job',
        'department'       => 'Department: :department',
        'type'             => 'Employment Type: :type',
        'join-date'        => 'Join Date: :date',
        'contract-end'     => 'Contract End: :date (:days day(s) remaining)',
        'attendance-month' => 'Attendance this month: :present present (:late late)',
        'performance-plan' => 'Latest Performance Plan: :title (Progress: :score)',
        'evaluation'       => 'Latest Performance Evaluation: :title (Score: :score)',
    ],

    'insights' => [
        'no-employee-module'        => 'The Employees module is not active.',
        'no-attendance-module'      => 'The Attendance module is not active.',
        'no-project-module'         => 'The Project module is not active.',
        'contract-heading'          => 'Employee Contracts Expiring (≤ 30 Days):',
        'no-expiring-contracts'     => 'No active employee contracts are expiring within the next 30 days.',
        'expired'                   => 'expired',
        'days-left'                 => 'day(s) left',
        'type-breakdown'            => 'Active Employees by Employment Type:',
        'unassigned'                => 'Unassigned',
        'attendance-today'          => 'Today\'s Attendance (:date) — Total: :total, Late: :late',
        'active-sites'              => 'Active Work Sites (Geofence): :sites',
        'performance-heading'       => 'Performance & Evaluation Summary:',
        'plans-count'               => 'Total Performance Plans: :count',
        'evaluations-count'         => 'Total Performance Evaluations: :count',
        'projects-summary'          => 'Projects Overview: :projects active project(s) with :tasks task(s).',
        'employee-summary'          => 'Total Active Employees: :employees across :departments department(s).',
        'personal-heading'          => 'Your Profile & Performance Summary (:name):',
        'personal-today-attendance' => 'Your attendance today: :in → :out:site',
        'personal-unlinked'         => 'Your account has personal-only access, but is not yet linked to an Employee profile. Please contact HR/Admin to link your user account.',
    ],

    'help' => "You can ask about:\n• Today's attendance & work sites\n• Expiring employee contracts (≤ 30 days) & employment types\n• Performance plans & evaluations\n• Project summary\n• Or type @ai inside any record's Chatter message.",
];
