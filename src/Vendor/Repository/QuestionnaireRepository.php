<?php

namespace App\Vendor\Repository;

use App\Vendor\Entity\Questionnaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class QuestionnaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Questionnaire::class);
    }

    public function save(Questionnaire $questionnaire)
    {
        $this->getEntityManager()->persist($questionnaire);
        $this->getEntityManager()->flush();
    }

    public function delete(Questionnaire $questionnaire)
    {
        $this->getEntityManager()->remove($questionnaire);
        $this->getEntityManager()->flush();
    }
}
