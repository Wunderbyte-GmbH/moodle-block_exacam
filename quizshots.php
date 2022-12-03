<?php
// This file is part of Exabis Quiz Camera
//
// (c) 2017 GTN - Global Training Network GmbH <office@gtn-solutions.com>
//
// Exabis Competence Grid is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This script is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You can find the GNU General Public License at <http://www.gnu.org/licenses/>.
//
// This copyright notice MUST APPEAR in all copies of the script!

require __DIR__.'/inc.php';

require_once("$CFG->libdir/formslib.php");

function getUsers($quizid) {
    global $DB;

    $users = $DB->get_records_sql("
	SELECT u.*
	FROM {user} u
	WHERE u.id IN (
    	    SELECT DISTINCT userid
	    FROM {files}
	    WHERE component='block_exacam' AND filearea='quizshot' AND filename<>'.'
	    AND contextid = ?
	)
	", [context_module::instance($quizid)->id]);
    return $users;
}

class quizshots_form extends moodleform {
    protected $courseid;
    protected $quizid;
    protected $idoverview;
    protected $dtstartdefault;
    protected $dtenddefault;

    public function __construct($courseid=null, $quizid=null, $idoverview='true', $dtstartdefault=0, $dtenddefault=0,  
	    $action=null, $customdata=null, $method='get', $target='', $attributes=null, $editable=true,
	    $ajaxformdata=null) {
	$this->courseid   = $courseid;
        $this->quizid     = $quizid;
	$this->idoverview = $idoverview;

	$this->dtstartdefault = intval($dtstartdefault);
	$this->dtenddefault   = intval($dtenddefault);

        parent::__construct($action, $customdata, $method, $target, $attributes, $editable, $ajaxformdata);
    }

    protected function definition() {
      global $CFG;

      $curyear = strftime('%Y');

      $dtopts = array(
          'startyear' => $curyear - 2,
          'stopyear'  => $curyear,
          'timezone'  => 99,
          'step'      => 30,
          'optional' => false,
      );

      $users = getUsers($this->quizid);
      $options = array( "all" => "Alle" );
      foreach( $users as $user) {
          $options[$user->id] = fullname($user);
      }

      $mform = $this->_form;
      $mform->addElement('hidden', 'courseid', $this->courseid);
      $mform->addElement('hidden', 'quizid', $this->quizid);
      $mform->addElement('hidden', 'idoverview', $this->idoverview);
      if( $this->dtstartdefault > 0 ) {
        $mform->setDefault('dtstart', $this->dtstartdefault);
      }
      $mform->addElement('date_time_selector', 'dtstart', 'von', $dtopts);
      if( $this->dtenddefault > 0 ) {
        $mform->setDefault('dtend', $this->dtenddefault);
      }
      $mform->addElement('date_time_selector', 'dtend', 'bis', $dtopts);
      $typeoptions = array(
        "all" => "Alle",
	"quizstart" => "Identitätsfestellung",
	"quizshots" => "Prüfungsaufnahmen"
      );
      $select = $mform->addElement('select', 'student', 'Studierende', $options);
      $select->setSelected('all');
      $selecttype = $mform->addElement('select', 'type', 'Bildtyp', $typeoptions);
      $selecttype->setSelected('quizstart');
      $this->add_action_buttons(false, 'suchen');
    }
}

$courseid = required_param('courseid', PARAM_INT);

$quizstartonly = optional_param('quizstartonly', false, PARAM_BOOL);
$idoverview    = optional_param('idoverview', false, PARAM_BOOL);
$student       = optional_param('student', 0, PARAM_INT);
$type          = optional_param('type', 'quizstart', PARAM_TEXT);

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($courseid);

require_login($course);

if (!block_exacam_is_teacher()) {
	throw new moodle_exception('no teacher');
}

$PAGE->set_url('/blocks/exacam/quizstart.php', array('courseid' => $courseid));
$PAGE->set_heading('');

$quizzes = get_coursemodules_in_course('quiz', $courseid);

$userid = optional_param('userid', 0, PARAM_INT);
$quizid = optional_param('quizid', 0, PARAM_INT);
$quiz = null;

if ($quizid) {
	if (!isset($quizzes[$quizid])) {
		throw new \Exception('quiz not found');
	}

	$quiz = $quizzes[$quizid];
}

echo $OUTPUT->header();

if ($userid && $quiz) {
	$user = $DB->get_record('user', ['id' => $userid]);

	echo '<h2>Quiz '.$quiz->name.' / Benutzer '.fullname($user).'</h2>';

	$fs = get_file_storage();
	$files = $fs->get_area_files(context_module::instance($quiz->id)->id, 'block_exacam', 'quizshot', $userid, 'timemodified DESC', false);

	echo '<div>';
	?>
	<style>
		.exacam-img {
			float: left;
			padding: 5px;
			margin: 5px;
			border: 1px solid black;
		}
		.exacam-img span {
			display: block;
			text-align: center;
			margin: 3px 0 -3px 0;
		}
	</style>
	<?php
	foreach ($files as $file) {

		if( $quizstartonly && !preg_match('/^quizstart_/', $file->get_filename()) ) {
		  continue;
		}

		$imageurl = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
		$img = html_writer::empty_tag('img', array('src' => $imageurl, 'class' => ''));
		echo '<div class="exacam-img"><div>'.$img.'</div><span>'.userdate($file->get_timemodified()).'</span></div>';
	}
	echo '</div>';
} else if ( $idoverview && $quiz  ) {
	$maximgs = 200;
	$quizinstance = $DB->get_record('quiz', ['id' => $quiz->instance]);
	$contextid = intval(context_module::instance($quizid)->id);	
        $qsf = new quizshots_form($courseid, $quizid, 'true', $quizinstance->timeopen, $quizinstance->timeclose);	
	$data = $qsf->get_data();

	$dtstart = intval($data->dtstart);
	$dtend   = intval($data->dtend);

	$filecount = -1;
	if( $dtstart > 0 && $dtend > 0 ) {
	    $countsql = 'SELECT count(*) AS files FROM {files} f '
                 . 'WHERE f.contextid = ' . $contextid . ' '
                 . 'AND f.component = \'block_exacam\' '
                 . 'AND f.filearea = \'quizshot\' '
                 . 'AND f.filename <> \'.\' '
		 . 'AND f.timecreated BETWEEN ' . $dtstart  . ' AND ' . $dtend . ' ';

            if( $type === 'quizstart' ) {
                $countsql .= 'AND f.filename LIKE \'quizstart_%\' ';
            } else if ( $type === 'quizshots'  ) {
                $countsql .= 'AND f.filename NOT LIKE \'quizstart_%\' ';
            }

            $studuserid = intval($student);
            if( $studuserid > 0 ) {
                $countsql .= 'AND f.userid = ' . $studuserid . ' ';
	    }
	    $res = $DB->get_record_sql($countsql);
	    $filecount = $res->files;
	    //echo $countsql . ' ' . $filecount;
	}

	$fileinfos = array();
	if( $dtstart > 0 && $dtend > 0 && $filecount > 0 && $filecount <= $maximgs ) {
	    $sql = 'SELECT f.*, u.firstname, u.lastname FROM {files} f '
		 . 'JOIN {user} u ON u.id = f.userid '
		 . 'AND f.contextid = ' . $contextid . ' '
	         . 'AND f.component = \'block_exacam\' '
		 . 'AND f.filearea = \'quizshot\' '
		 . 'AND f.filename <> \'.\' '
	         . 'AND f.timecreated BETWEEN ' . $dtstart  . ' AND ' . $dtend . ' '; 

	    if( $type === 'quizstart' ) {
                $sql .= 'AND f.filename LIKE \'quizstart_%\' ';
	    } else if ( $type === 'quizshots'  ) {
                $sql .= 'AND f.filename NOT LIKE \'quizstart_%\' ';
	    }
	
	    $studuserid = intval($student);
	    if( $studuserid > 0 ) {
                $sql .= 'AND f.userid = ' . $studuserid . ' ';
	    }

	    $sql .= 'ORDER BY u.lastname ASC, f.timecreated DESC' 
	         .  ';';

	    $fileinfos = $DB->get_records_sql($sql);
	}

//	echo '<pre>' . $sql . '</pre>';
//	echo '<pre>' . $dtstart . ' | ' . $dtend . ' | ' . print_r($quizinstance, true)  . '</pre>';

	echo '<div style="max-width: 740px;">';
	$qsf->display();
	echo '</div>';

	echo '<div>';
?>
	<style>
		.exacam-img {
			float: left;
			padding: 5px;
			margin: 5px;
			border: 1px solid black;
		}
		.exacam-img span {
			display: block;
			text-align: center;
			margin: 3px 0 -3px 0;
		}
	</style>
<?php
	
	if( $filecount < 1 ) {
          echo '<p>Keine Bilder gefunden.</p>';
	}

	if( $filecount > $maximgs ) {
          echo '<p style="color: red;">Ihre Abfrage liefert mehr als ' . $maximgs . ' Bilder. Bitte schränken Sie ihre Abfrage mehr ein.</p>';
	}

	foreach( $fileinfos as $fileinfo ) {
	    $imageurl = \moodle_url::make_pluginfile_url($fileinfo->contextid, $fileinfo->component, $fileinfo->filearea, $fileinfo->itemid, $fileinfo->filepath, $fileinfo->filename);
	    $img = html_writer::empty_tag('img', array('src' => $imageurl, 'class' => ''));
	    echo '<div class="exacam-img"><div>'.$img.'</div><span>' . $fileinfo->lastname . ' ' . $fileinfo->firstname . ' &ndash; ' . userdate($fileinfo->timemodified) . '</span></div>';
	}
	echo '</div>';

} else {
	if ($quiz) {
		// loop one quiz
		$loop = [$quiz];
	} else {
		$loop = $quizzes;
	}
	foreach ($loop as $quiz) {
		echo '<h2>Quiz '.$quiz->name.'</h2>';
/*
		$users = $DB->get_records_sql("
			SELECT u.*
			FROM {user} u
			WHERE u.id IN (
				SELECT DISTINCT userid
				FROM {files}
				WHERE component='block_exacam' AND filearea='quizshot' AND filename<>'.'
				AND contextid = ?
			)
		", [context_module::instance($quiz->id)->id]);
 */
		$users = getUsers($quiz->id);
		foreach ($users as $user) {
			if( $quizstartonly  ) {
      		  	    echo '<a href="'.$_SERVER['PHP_SELF'].'?courseid='.$courseid.'&quizstartonly=true'.'&quizid='.$quiz->id.'&userid='.$user->id.'">'.fullname($user).'</a><br/>';
			} else {
			    echo '<a href="'.$_SERVER['PHP_SELF'].'?courseid='.$courseid.'&quizid='.$quiz->id.'&userid='.$user->id.'">'.fullname($user).'</a><br/>';
			}
		}
	}
}

echo $OUTPUT->footer();

