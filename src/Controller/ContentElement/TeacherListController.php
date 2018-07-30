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

    public function __construct(Connection $db)
    {
        $this->database = $db;
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