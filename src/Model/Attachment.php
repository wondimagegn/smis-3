<?php
/**
 * Attachment Model File
 *
 * Copyright (c) 2007-2011 David Persson
 *
 * Distributed under the terms of the MIT License.
 * Redistributions of files must retain the above copyright notice.
 *
 * PHP version 5
 * CakePHP version 1.3
 *
 * @package    media
 * @subpackage media.models
 * @copyright  2007-2011 David Persson <davidpersson@gmx.de>
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link       http://github.com/davidpersson/media
 */

/**
 * Attachment Model Class
 *
 * A ready-to-use model combining multiple behaviors.
 *
 * @package    media
 * @subpackage media.models
 */
class Attachment extends AppModel {

/**
 * Name of model
 *
 * @var string
 * @access public
 */
	var $name = 'Attachment';

/**
 * Name of table to use
 *
 * @var mixed
 * @access public
 */
	var $useTable = 'attachments';

/**
 * actsAs property
 *
 * @var array
 * @access public
 */
	var $actsAs = array(
		'Media.Transfer' => array(
			'trustClient' => false,
			'transferDirectory' => MEDIA_TRANSFER,
			'createDirectory' => true,
			'alternativeFile' => 100
		),
		/*'Media.Generator' => array(
			'baseDirectory' => MEDIA_TRANSFER,
			'filterDirectory' => MEDIA_FILTER,
			'createDirectory' => true,
		),
		*/
		'Media.Polymorphic',
		'Media.Coupler' => array(
			'baseDirectory' => MEDIA_TRANSFER
		),
		
		'Media.Meta' => array(
			'level' => 2
		)
	);
	
/**
 * Validation rules for file and alternative fields
 *
 * For more information on the rules used here
 * see the source of TransferBehavior and MediaBehavior or
 * the test case for MediaValidation.
 *
 * If you experience problems with your model not validating,
 * try commenting the mimeType rule or providing less strict
 * settings for single rules.
 *
 * `checkExtension()` and `checkMimeType()` take both a blacklist and
 * a whitelist. If you are on windows make sure that you addtionally
 * specify the `'tmp'` extension in case you are using a whitelist.
 *
 * @var array
 * @access public
 */
 
	var $validate = array(
		'file' => array(
			'resource'   => array('rule' => 'checkResource'),
			'access'     => array('rule' => 'checkAccess'),
			'location'   => array('rule' => array('checkLocation', array(
				MEDIA_TRANSFER, '/tmp/'
			))),
			'permission' => array('rule' => array('checkPermission', '*')),
			'size'=> array(
			    'rule' => array('checkSize', '10M'),
			    'message'=>'The maximum file size allowed for upload is 9M'
			 ),
			'pixels'     => array('rule' => array('checkPixels', '1600x1600')),
			'extension'  => array('rule' => array('checkExtension', false, array(
				'jpg', 'jpeg', 'png', 'tif', 'tiff','doc','docx', 'gif', 'pdf', 'tmp'
			),'message'=>'The extension of the file you uploaded is not supported.
			 Please upload a file with the extenstion jpg, jpeg,png,tif,tiff,gif,pdf ')),
			
			'mimeType'   => array('rule' => array('checkMimeType', false, array(
				'image/jpeg', 'image/png', 'image/tiff', 'image/gif', 
				'application/pdf'
		    ),'message'=>'Not supported mimeType'))
		),
		'alternative' => array(
			'rule'       => 'checkRepresent',
			'on'         => 'create',
			'required'   => false,
			'allowEmpty' => true,
		)
	);
/**
 * Uncomment to get fancy path field.
 *
 * @var array
 * @access public
 */
	 var $virtualFields = array(
		'path' => "CONCAT_WS('/', dirname, basename)"
	 );

/**
 * Generate a version of a file
 *
 * Uncomment to force Generator Behavior to use this method when
 * generating versions of files.
 *
 * If you want to fall back from your code back to the default method use:
 * `return $this->Behaviors->Generator->makeVersion($this, $file, $process);`
 *
 * $process an array with the following contents:
 *  directory - The destination directory (If this method was called
 *              by `make()` the directory is already created)
 *  version - The version requested to be processed (e.g. `l`)
 *  instructions - An array containing which names of methods to be called
 *
 * @param file $file Absolute path to source file
 * @param array $process version, directory, instructions
 * @return boolean `true` if version for the file was successfully stored
 */
	//function makeVersion($file, $process) {
	// }

/**
 * Returns the relative path to the destination file
 *
 * Uncomment to force Transfer Behavior to use this method when
 * determining the destination path instead of the builtin one.
 *
 * @param array $via Information about the temporary file
 * @param array $from Information about the source file
 * @return string The path to the destination file or false
 */
	// function transferTo($via, $from) {
	// }
	
	//A special method in  used by the transfer 
   // behavior in order to make it use non-guessable file names.
	function transferTo($via, $from) {
		extract($from);

		$irregular = array(
			'image' => 'img',
			'text' => 'txt'
		);
		$name = Mime_Type::guessName($mimeType ? $mimeType : $file);

		if (isset($irregular[$name])) {
			$short = $irregular[$name];
		} else {
			$short = substr($name, 0, 3);
		}

		$path  = $short . DS;
		$path .= uniqid(); // <--- This is the important part.
		$path .= !empty($extension) ? '.' . strtolower($extension) : null;

		return $path;
	}
	/*
	Deleting works as you'd expect (with one exception). First it is assumed that
    there is a 1:1 relationship between files and records. So when deleting a
    record, coupled to a file, the Coupler behavior will also go and delete that
    file. Now that one exception is generated versions derived from the coupled file
    *will not* get deleted automatically. For this piece of functionality you'll
    need to implement the logic on your own. Following a snippet which does this in
    an exemplaric way.  Add it to to the model you've attached Generator and
    Coupler behaviors to.
  */
	public function beforeDelete($cascade = true) {
		if (!$cascade) {
			return true;
		}

		$result = $this->find('first', array(
			'conditions' => array($this->primaryKey => $this->id),
			'fields'	 => array('dirname', 'basename'),
			'recursive'  => -1
		));
		if (empty($result)) {
			return false;
		}

		$pattern  = MEDIA_FILTER . "*/";
		$pattern .= $result[$this->alias]['dirname'] . '/';
		$pattern .= pathinfo($result[$this->alias]['basename'], PATHINFO_FILENAME);

		$files = glob("{$pattern}.*");

		$name = Mime_Type::guessName($result[$this->alias]['basename']);
		$versions = array_keys(Configure::read('Media.filter.' . $name));

		if (count($files) > count($versions)) {
			$message  = 'MediaFile::beforeDelete - ';
			$message .= "Pattern `{$pattern}` matched more than number of versions. ";
			$message .= "Failing deletion of versions and record for `Media@{$this->id}`.";
			CakeLog::write('warning', $message);
			return false;
		}

		foreach ($files as $file) {
			$File = new File($file);

			if (!$File->delete()) {
				return false;
			}
		}
		return true;
	}
}
?>
