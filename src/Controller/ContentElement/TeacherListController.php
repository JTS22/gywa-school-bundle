<?php


namespace GyWa\SchoolBundle\Controller\ContentElement;


use Contao\ContentModel;

use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;

use Contao\FilesModel;

use Contao\FrontendTemplate;

use Contao\Template;

use Doctrine\DBAL\Connection;

use Doctrine\DBAL\FetchMode;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Validator\Constraints\Uuid;


class TeacherListController extends AbstractContentElementController
{

	private $database;

	private $threshold = 100;
	private $teacherImgDir = 'files/daten/ueber-uns/lehrerkollegium/';
	private $logFile = 'files/daten/ueber-uns/lehrerkollegium/tmp.txt';
	private $emailRecipientFull = 'Website-Gruppe <homepage-support@gy-waldstrasse.de>';

	public function __construct(Connection $db)
	{
		$this->database = $db;

		// find too large teacher images in teacher image director
		$this->checkImageFilesizes(['kollegium-2017.jpg', 'tmp.txt', 'logo-blass.svg']);
	}

	// check in the teacher's images directory for files larger than threshold and write email to recipient if any are found
	private function checkImageFilesizes(array $whitelist = [])
	{
		if(!$dir = opendir($this->teacherImgDir))
			return false;

		$files = $non_jpg_files = [];
		while(($file = readdir()) !== false) {
			$fullPath = $this->teacherImgDir.$file;
			if(is_file($fullPath) && !in_array($file, $whitelist)) {
				// file is larger than threshold
				if(($size = filesize($fullPath) / 1000) > $this->threshold)
					$files[$file] = round($size);

				// extension is not .jpg
				$pinfo = pathinfo($file, PATHINFO_EXTENSION);
				if($pinfo !== 'jpg' && $pinfo)
					$non_jpg_files[] = $file;
			}
		}

		closedir();

		// no large images found
		if(empty($files) && empty($non_jpg_files)) {
			unlink($this->logFile);

			return true;
		}

		krsort($files); // sort files by filesize descending (most relevant come first)

		// log list of files to prevent multiple mails
		if($this->newImagesFound($files, $non_jpg_files))
			$mail = $this->sendMail($files, $non_jpg_files);

		return true;
	}

	private function newImagesFound(array $files = [], array $non_jpg_files = [])
	{
		$handle = fopen($this->logFile, 'a+');

		$old_files = str_replace("\n", '', fgets($handle));
		$old_non_jpg_files = str_replace("\n", '', fgets($handle));

		ftruncate($handle, 0); // overwrite log file

		$files = (empty($files) ? '' : implode(';', array_keys($files)));
		$non_jpg_files = (empty($non_jpg_files) ? '' : implode(';', $non_jpg_files));
		fwrite($handle, $files."\n".$non_jpg_files);

		fclose($handle);

		return ($old_files != $files || $old_non_jpg_files != $non_jpg_files);
	}

	private function sendMail(array $files = [], array $non_jpg_files = [], string $subject = '[Bot] Probleme mit den Bildern im Leherverzeichnis')
	{
		$message[] = 'Hallo!';
		$message[] = '';
		$message[] = 'Ich bin eine automatisch generierte Nachricht und informiere die Website-Gruppe, wenn es Probleme mit den Bildern im Leherverzeichnis (/files/daten/ueber-uns/lehrerkollegium/) gibt.';
		$message[] = '';

		if(!empty($files)) {
			$message[] = 'Ich habe folgende Bilder gefunden, die das Größenlimit von <strong>'.$this->threshold.' kB</strong> überschreiten:';

			$filelist = '';
			foreach($files as $file => $size)
				$filelist .= '<li>'.$file.' ('.number_format($size, 0, ',', '.').' kB)</li>';

			$message[] = '<ol>'.$filelist.'</ol>Bitte reduziert die Bildgröße, damit die Lehrerseite weiterhin schnell lädt. Dazu könnt ihr die Größe des Bilder mit GIMP auf eine Breite von 400 px im Seitenverhältnis 1:√2 (DIN-A4 im Hochformat), sodass die Bilder dann ca. 400x567 px groß sind. Außerdem sollten sie im JPG-Format und die Qualitätseinstellung beim Export nicht über 70 sein.';
			$message[] = '';
		}

		if(!empty($non_jpg_files)) {
			$message[] = 'Ich habe folgende Bilder gefunden, die nicht die Dateiendung <strong>".jpg"</strong> haben:';

			$filelist = '';
			foreach($non_jpg_files as $file)
				$filelist .= '<li>'.$file.'</li>';

			$message[] = '<ol>'.$filelist.'</ol>Bitte konvertiert die Dateien in ".jpg"-Dateien, da diese Fotos am speichereffizientesten darstellen und die Website so schneller laden kann. Am Ende muss noch der Bildpfad in Contao unter Lehrer > Bearbeiten > Foto angepasst werden.';
			$message[] = 'Hinweis: Hier erscheinen auch Dateien mit der Dateiendung ".JPG", die der Konsistenz halber in ".jpg" umbenannt werden sollten.';
			$message[] = '';
		}

		$message[] = 'Danke für eure Mithilfe, die Website nutzerfreundlich zu halten!';
		$message[] = '';
		$message[] = 'Der CheckImageFilesizes-Bot';
		$message = str_replace('\n.', '\n..', '<html><body>'.implode('<br>', $message).'</body></html>'); // implode with newlines and preseve dots at the beginning of line

		$headers[] = 'MIME-Version: 1.0';
		$headers[] = 'Content-type: text/html; charset=utf-8';
		$headers[] = 'From: CheckImageFilesizes-Bot <website-gruppe@gy-waldstrasse.de>';
		$headers = str_replace('\n.', '\n..', implode("\r\n", $headers)); // implode with newlines and preseve dots at the beginning of line

		mail($this->emailRecipientFull, $subject, $message, $headers);
	}

	private function escapeHTML($string)
	{
		return str_replace(['ä', 'ö', 'ü', '_', ' '], ['ae', 'oe', 'ue', '-', '-'], mb_strtolower($string));
	}

	/* I'm sure this is highly performance-efficient... lets hope nobody wants to have 1000+ subjects to check... */
	private function applySubjectFilter(array $teacherSubjects, array $filterSubjects, bool $requireAll): bool
	{
		return $requireAll ? !array_diff($filterSubjects, $teacherSubjects) : (bool)array_intersect($filterSubjects, $teacherSubjects);
	}

	protected function getResponse(Template $template, ContentModel $model, Request $request): Response
	{
		$subjectStatement = $this->database->prepare('SELECT * FROM tl_subject');
		$subjectStatement->execute();
		$result = $subjectStatement->fetchAll(FetchMode::ASSOCIATIVE);
		$subjectStatement->closeCursor();

		$allSubjects = array();

		if (!empty($result)) {

			foreach ($result as $teacherRow) {

				$allSubjects[$this->escapeHTML($teacherRow['abbreviation'])] = [
					'referencePage' => $teacherRow['referencePage'],
					'abbreviation' => $teacherRow['abbreviation'],
					'title' => $teacherRow['title']
				];
			}
		}

		unset($subjectStatement);
		$subjectStatement = $this->database->prepare('SELECT abbreviation FROM tl_subject WHERE id = ? LIMIT 1');
		$teacherStatement = $this->database->prepare('SELECT * FROM tl_teacher WHERE category = ? ORDER BY lastName ASC');

		$allCategories = array();

		$categoryStatement = $this->database->prepare("SELECT * FROM tl_teacher_category");
		$categoryStatement->execute();

		$allowedCategories = unserialize($model->gywaTeacherCategoryFilter);

		while ($category = $categoryStatement->fetch(FetchMode::STANDARD_OBJECT)) {

			if (!in_array($category->id, $allowedCategories)) continue;

			$teachersInCategory = array();
			$teacherStatement->execute([$category->id]);
			$result = $teacherStatement->fetchAll(FetchMode::STANDARD_OBJECT);

			if (!empty($result)) {
				foreach ($result as $teacherRow) {

					if (!$this->applySubjectFilter(unserialize($teacherRow->subjects), unserialize($model->gywaSubjectFilter), $model->gywaSubjectFilterRequireAll)) continue;

					$name = $teacherRow->lastName . ',' . (strlen($teacherRow->lastName) < 15 ? '<br>' : ' ') . (!is_null($teacherRow->prefix) ? $teacherRow->prefix . '&nbsp;' : '') . $teacherRow->firstName;
					$subjects = array();

					if ($teacherRow->subjects) {
						foreach (unserialize($teacherRow->subjects) as $subjectID) {
							$subjectStatement->execute([$subjectID]);
							$result = $subjectStatement->fetch(FetchMode::ASSOCIATIVE);
							$subjects[] = $this->escapeHTML($result['abbreviation']);
						}
						sort($subjects);
					}
					$emailAddress = $this->escapeHTML(substr($teacherRow->firstName, 0, 1) . '.' . $teacherRow->lastName);

					if ($teacherRow->image) {
						$imagePath = FilesModel::findByUuid($teacherRow->image)->path;
					} else {
						$imagePath = FilesModel::findByUuid($model->gywaDefaultTeacherImage)->path;
					}

					array_push($teachersInCategory, array(
						'name' => $name,
						'abbreviation' => $teacherRow->abbreviation,
						'subjects' => $subjects,
						'image' => $imagePath,
						'emailAddress' => $emailAddress
					));
					unset($subjects);
				}
			}

			if (!$model->gywaDisplayEmptyCategories && empty($teachersInCategory)) continue;

			$categoryName = str_replace(["ä", "ö", "ü", "_", " ", "/", ","], ["ae", "oe", "ue", "-", "-", "-", "-"], mb_strtolower($category->title));
			array_push($allCategories, array(
				'title' => $category->title,
				'alias' => $categoryName,
				'cssClass' => $category->cssClass,
				'items' => $teachersInCategory));

		}

		$togglerTemplate = new FrontendTemplate('ce_category_toggler');
		$togglerTemplate->setData(array('categoryList' => $allCategories, 'allName' => sprintf($GLOBALS['TL_LANG']['MSC']['gywa_category']['all'])));

		$template->categories = $allCategories;
		$template->emailDomain = $model->gywaTeacherEmailDomain;
		$template->togglerCode = $togglerTemplate->parse();
		$template->subjects = $allSubjects;
		$template->lang = array(
			'explanation' => sprintf($GLOBALS['TL_LANG']['MSC']['gywa_teacher']['explanation']),
			'email' => sprintf($GLOBALS['TL_LANG']['MSC']['gywa_teacher']['email']),
			'abbreviation' => sprintf($GLOBALS['TL_LANG']['MSC']['gywa_teacher']['abbreviation'])
		);

		return $template->getResponse();

	}


}