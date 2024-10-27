<?php
defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => '\core\event\user_enrolment_created',
        'callback'  => 'local_sectiondateevents\observer::user_enrolled',
    ],
    [
        'eventname' => '\core\event\user_enrolment_deleted',
        'callback'  => 'local_sectiondateevents\observer::user_unenrolled',
    ],
    [
        'eventname' => '\core\event\course_section_updated',
        'callback'  => 'local_sectiondateevents\observer::course_updated_or_section_added',
    ],
    [
        'eventname' => '\core\event\course_section_created',
        'callback'  => 'local_sectiondateevents\observer::course_updated_or_section_added',
    ]
];
