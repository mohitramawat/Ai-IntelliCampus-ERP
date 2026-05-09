<?php

return [
    'admin' => [
        ['name' => 'Dashboard',         'route' => 'admin.dashboard',         'icon' => 'dashboard'],
        [
            'name' => 'Students',
            'icon' => 'group',
            'submenu' => [
                ['name' => 'All Students', 'route' => 'admin.students.index', 'icon' => 'group'],
            ]
        ],
        [
            'name' => 'Staff',
            'icon' => 'badge',
            'submenu' => [
                ['name' => 'All Staff',    'route' => 'admin.staff.index',  'icon' => 'groups'],
                ['name' => 'Add Staff',    'route' => 'admin.staff.create', 'icon' => 'person_add'],
            ]
        ],
        ['name' => 'Master Data',        'route' => 'admin.master.index',      'icon' => 'database'],
    ],
    'hod' => [
        ['name' => 'Dashboard',          'route' => 'hod.dashboard',           'icon' => 'dashboard'],
        ['name' => 'Dept. Teachers',     'route' => 'hod.teachers.index',      'icon' => 'supervisor_account'],
        ['name' => 'Dept. Students',     'route' => 'hod.students.index',      'icon' => 'group'],
        ['name' => 'AI Risk Prediction', 'route' => 'hod.risk.index',          'icon' => 'psychology'],
    ],
    'accounts' => [
        ['name' => 'Dashboard',          'route' => 'accounts.dashboard',      'icon' => 'dashboard'],
        [
            'name' => 'Fee Management',
            'icon' => 'payments',
            'submenu' => [
                ['name' => 'All Transactions', 'route' => 'accounts.fees.transactions', 'icon' => 'history'],
                ['name' => 'Student Dues',      'route' => 'accounts.fees.dues',         'icon' => 'account_balance_wallet'],
                ['name' => 'Defaulters List',  'route' => 'accounts.fees.defaulters',   'icon' => 'warning'],
            ]
        ],
        ['name' => 'Revenue Reports',    'route' => 'accounts.fees.reports',   'icon' => 'bar_chart'],
        ['name' => 'Fee Structures',     'route' => 'accounts.fees.structures','icon' => 'list_alt'],
        ['name' => 'Apply Group Fine',   'route' => 'accounts.fines.index',    'icon' => 'gavel'],
    ],
    'writer' => [
        ['name' => 'Dashboard',          'route' => 'writer.dashboard',        'icon' => 'dashboard'],
        [
            'name' => 'Students',
            'icon' => 'group',
            'submenu' => [
                ['name' => 'All Students',       'route' => 'writer.students.index',   'icon' => 'group'],
                ['name' => 'Create Student',     'route' => 'writer.students.create',  'icon' => 'person_add'],
                ['name' => 'Pending Documents',  'route' => 'writer.students.pending-documents', 'icon' => 'folder_open'],
                ['name' => 'Batch Promotion',    'route' => 'writer.promotion.index',  'icon' => 'upgrade'],
            ]
        ],
        ['name' => 'Teachers',           'route' => 'writer.teachers.index',   'icon' => 'person_outline'],
        // Master Data
        ['name' => 'Departments',        'route' => 'writer.master.departments.index', 'icon' => 'account_tree'],
        ['name' => 'Courses',            'route' => 'writer.master.courses.index',     'icon' => 'school'],
        ['name' => 'Batches',            'route' => 'writer.master.batches.index',     'icon' => 'layers'],
        ['name' => 'Subjects',           'route' => 'writer.master.subjects.index',    'icon' => 'menu_book'],
        ['name' => 'Fee Structures',     'route' => 'writer.master.fees.index',        'icon' => 'payments'],
    ],
    'teacher' => [
        ['name' => 'Dashboard',          'route' => 'teacher.dashboard',       'icon' => 'school'],
        ['name' => 'Mark Attendance',    'route' => 'teacher.attendance.index','icon' => 'touch_app'],
        ['name' => 'AI Risk Prediction', 'route' => 'teacher.risk.index',     'icon' => 'psychology'],
        // ['name' => 'Ultrasonic Att. 🚀',  'route' => 'teacher.ultrasonic.index','icon' => 'radar'],
    ],
    'student' => [
        ['name' => 'Dashboard',          'route' => 'student.dashboard',       'icon' => 'dashboard'],
        ['name' => 'My Attendance',      'route' => 'student.attendance.index','icon' => 'touch_app'],
        // ['name' => 'Ultrasonic Att. 🚀',  'route' => 'student.ultrasonic.index','icon' => 'radar'],
        ['name' => 'Profile Settings',   'route' => 'student.profile.index',   'icon' => 'account_circle'],
        ['name' => 'Fee Payment',        'route' => 'student.fees.index',      'icon' => 'payments'],
        ['name' => 'My Documents',       'route' => 'student.documents.index', 'icon' => 'folder_open'],
        ['name' => 'Change Password',    'route' => 'student.password.change', 'icon' => 'lock_reset'],
    ],
];
