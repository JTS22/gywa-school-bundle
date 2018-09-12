<?php
declare(strict_types=1);

namespace GyWa\SchoolBundle;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\FetchMode;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

class SubjectManager
{

    private $logger;
    private $database;

    /**
     * SubjectManager constructor.
     * @param $logger
     * @param $database
     */
    public function __construct(Connection $connection, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->database = $connection;
    }

    public function getAllSubjects() : array {
        $result = array();

        try {
            $statement = $this->database->prepare("SELECT id FROM tl_subject");
            if($statement->execute()) {
                while ($idObj = $statement->fetch(FetchMode::STANDARD_OBJECT)) {
                    array_push($result, $idObj->id);
                }
            }
        } catch (DBALException $e) {
            $this->logger->error("Teacher categories could not be loaded from database! Please ensure the table tl_subject exists!");
        }
        return $result;
    }

    public function onSubjectAbbrSaved(String $abbreviation, \DataContainer $dataContainer) : String {
        if (empty($abbreviation)) {
            try {
                $getDBCount = $this->database->prepare("SELECT * FROM tl_subject WHERE LOWER(abbreviation)=?");
            } catch (DBALException $e) {
            }
            for ($i = 2; $i < 5; $i++) {
                $abbreviation = substr($dataContainer->activeRecord->title, 0, $i);
                $getDBCount->execute(array(strtolower($abbreviation)));
                if ($getDBCount->fetch() === false) {
                    $this->logger->info('Abbreviation ' . $abbreviation . ' generated for subject ' . $dataContainer->activeRecord->title . '.');
                    return $abbreviation;
                }
            }
            throw new Exception(sprintf($GLOBALS['TL_LANG']['tl_subject']['abbreviationNotGenerated']));
        } else {
            $this->logger->info('Abbreviation ' . $abbreviation . ' accepted for subject ' . $dataContainer->activeRecord->title . '.');
            return $abbreviation;
        }
    }


}