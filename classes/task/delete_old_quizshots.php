<?php
// 20210104 harald.bamberger@donau-uni.ac.at task to cleanup quizshots

namespace block_exacam\task;
 
defined('MOODLE_INTERNAL') || die();

/**
 * Find quizshots older than a threshold and delete them
 */
class delete_old_quizshots extends \core\task\scheduled_task {
    const DEBUG = false;

//  PRODUCTION SETTINGS
    const OLDERTHAN     = '-6 months';
    const MAXIMGSPERRUN = 100;
    const ADDSQLCLAUSE  = '';

//  DEBUG SETTINGS
//  const OLDERTHAN     = '-2 days';
//  const MAXIMGSPERRUN = 3;
//  const ADDSQLCLAUSE  = 'AND userid = 31208 '; // only Quizshots from hbamberger-teststudent

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('delete_old_quizshots', 'block_exacam');
    }
 
    /**
     * Execute the task.
     */
    public function execute() {
	global $DB;

        mtrace('DUK Exacam: task to cleanup quizshots is running.');
        $threshold = strtotime(self::OLDERTHAN);
	mtrace('DUK Exacam: threshold is ' . $threshold);

	$sql = 'SELECT * FROM {files} '
	     . 'WHERE component = \'block_exacam\' '
	     . 'AND filearea = \'quizshot\' '
	     . 'AND filename <> \'.\' '
	     . 'AND timemodified < ' . $threshold  . ' '
	     . self::ADDSQLCLAUSE
	     . 'LIMIT ' . self::MAXIMGSPERRUN . ';';

	if( self::DEBUG ) {
	    mtrace('DUK Exacam: ' . $sql);
	}

	$fileinfos = $DB->get_records_sql($sql);
	mtrace('DUK Exacam: ' . count($fileinfos)  . ' files found to be deleted.');
	if( self::DEBUG ) {
	    $this->dryrun($fileinfos);
	} else {
	    $this->delete_quizshots($fileinfos);
	}
    }

    protected function delete_quizshots($fileinfos) {
	$deleted = 0;
	$errors  = 0;
	
	$fs = get_file_storage();
	foreach ($fileinfos as $fileinfo) {
	    $file = $fs->get_file($fileinfo->contextid, $fileinfo->component, $fileinfo->filearea, $fileinfo->itemid, $fileinfo->filepath, $fileinfo->filename);	    
	    if( $file ) {
		try {
		  $file->delete();
                  $path = $this->get_filepath($fileinfo);
		  if( file_exists($path) ) {
		    throw new \Exception('ERROR: File ' . $path . ' still exists.');
		  }
		  $deleted++;
		} catch (\Exception $ex) {
		  $errors++;
		  mtrace('DUK Exacam: Error deleting file. ' . $ex->getMessage() . ' ' . print_r($file, true));
		}
	    }
	}
	mtrace('DUK Exacam: ' . $deleted  . ' files deleted. ' . $errors . ' Errors.');
    }

    protected function dryrun($fileinfos) {
	foreach ($fileinfos as $fileinfo) {
	    $imageurl = \moodle_url::make_pluginfile_url($fileinfo->contextid, $fileinfo->component, $fileinfo->filearea, $fileinfo->itemid, $fileinfo->filepath, $fileinfo->filename);
	    mtrace('DUK Exacam: ' . $imageurl->__toString());
	    $path = $this->get_filepath($fileinfo);
	    $iswriteable = (is_writable($path)) ? 'is writeable' : 'is not writeable';
	    mtrace('DUK Exacam: ' . $path . ' ' . $iswriteable);
	}
        mtrace('DUK Exacam: ' . print_r($fileinfos, true));
    }

    protected function get_filepath($fileinfo) {
	global $CFG;
        $filepath = $CFG->dataroot . '/filedir' . '/' . $this->get_contentdir_from_hash($fileinfo->contenthash) . '/' . $fileinfo->contenthash;
        return $filepath;
    }

    protected function get_contentdir_from_hash($contenthash) {
        $l1 = $contenthash[0] . $contenthash[1];
        $l2 = $contenthash[2] . $contenthash[3];
        return "$l1/$l2";
    }    
}
