<?php
declare(strict_types=1);

namespace GyWa\SchoolBundle;


use Doctrine\DBAL\Connection;
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

    public function onSubjectAbbrSaved(String $abbreviation, \DataContainer $dataContainer) : String {
        if (empty($abbreviation)) {
            $getDBCount = $this->database->prepare("SELECT * FROM tl_subject WHERE LOWER(abbreviation)=?");
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
            return $abbreviation;
            $this->logger->info('Abbreviation ' . $abbreviation . ' accepted for subject ' . $dataContainer->activeRecord->title . '.');
        }
    }


}