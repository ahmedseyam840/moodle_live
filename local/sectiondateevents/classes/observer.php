<?php
namespace local_sectiondateevents;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/calendar/lib.php');

class observer {
    public static function user_enrolled(\core\event\user_enrolment_created $event) {
        global $DB;

        $userid = $event->relateduserid;
        $courseid = $event->courseid;

        // Fetch all sections for the course.
        $sections = $DB->get_records('course_sections', ['course' => $courseid, 'visible' => 1]);

        foreach ($sections as $section) {
            if ( $section->custom_date != null) {
                $sectiondate = $section->custom_date;

                $eventdata = new \stdClass();
                $eventdata->name = 'Course Section ' . $section->name;
                $eventdata->description = $section->summary;
                $eventdata->format = FORMAT_HTML;
                $eventdata->courseid = $courseid;
                $eventdata->userid = $userid;
                $eventdata->timestart = $sectiondate;
                $eventdata->timeduration = 3600;
                $eventdata->visible = 1;
                $eventdata->eventtype = 'user';

                try {
                    \calendar_event::create($eventdata);
                } catch (\Exception $e) {
                    debugging('Failed to create calendar event: ' . $e->getMessage());
                }
            }
        }
    }

    public static function user_unenrolled(\core\event\user_enrolment_deleted $event) {
        global $DB;

        $userid = $event->relateduserid;
        $courseid = $event->courseid;

        // Find and delete events associated with this user and course.
        $events = $DB->get_records('event', [
            'userid' => $userid,
            'courseid' => $courseid,
            'eventtype' => 'user'
        ]);

        foreach ($events as $event) {
            try {
                $calendarevent = \calendar_event::load($event->id);
                $calendarevent->delete();
            } catch (\Exception $e) {
                debugging('Failed to delete calendar event: ' . $e->getMessage());
            }
        }
    }
}
