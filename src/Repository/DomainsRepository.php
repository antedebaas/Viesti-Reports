<?php

namespace App\Repository;

use App\Entity\Domains;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Domains>
 *
 * @method Domains|null find($id, $lockMode = null, $lockVersion = null)
 * @method Domains|null findOneBy(array $criteria, array $orderBy = null)
 * @method Domains[]    findAll()
 * @method Domains[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DomainsRepository extends ServiceEntityRepository
{
    private UsersRepository $usersRepository;

    public function __construct(ManagerRegistry $registry, UsersRepository $usersRepository)
    {
        parent::__construct($registry, Domains::class);
        $this->usersRepository = $usersRepository;
    }

    //    /**
    //     * @return Domains[] Returns an array of Domains objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxDMARC_Results(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Domains
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function validate_bimiv1_svg_file($svgContent) {
        $result = ['result' => true, 'errors' => []];
      
        // Check for '<?xml version' tag
        if (!preg_match('/^\<\?xml version="([^\"]+)"/', $svgContent)) {
          $result['result'] = false;
          $result['errors'][] = 'Invalid SVG: Missing <?xml version> tag';
        }
      
        // Check for root element '<svg>'
        if (!preg_match('/\<svg/', $svgContent)) {
          $result['result'] = false;
          $result['errors'][] = 'Invalid SVG: Missing <svg> root element';
        }
      
        // Check for mandatory attributes on '<svg>' element (baseProfile, version, no x,y)
        if (!preg_match('/<svg\s+[^>]*?(baseProfile|version|x|y)\s*=\s*["\']([^>]*?)["\']/i', $svgContent, $matches)) {
          if (!isset($matches[1]) || (isset($matches[1]) && ($matches[1] == 'baseProfile' || $matches[1] == 'version' || $matches[1] == 'x' || $matches[1] == 'y'))) {
            $result['result'] = false;
            $result['errors'][] = 'Invalid SVG: Missing mandatory attributes (baseProfile="tiny-ps", version="1.2") or unexpected attributes (x, y) on <svg> element';
          }
        } else {
          // Check baseProfile and version values
          if (isset($matches[1]) && isset($matches[3])) {
            if ($matches[1] == 'baseProfile' && strtolower($matches[3]) != 'tiny-ps') {
              $result['result'] = false;
              $result['errors'][] = 'Invalid SVG: baseProfile attribute on <svg> element should be set to "tiny-ps".';
            } else if ($matches[1] == 'version' && $matches[3] != '1.2') {
              $result['result'] = false;
              $result['errors'][] = 'Invalid SVG: version attribute on <svg> element should be set to "1.2".';
            }
          }
        }
      
        // Check for required elements (<title>) and optional (<desc>)
        if (!preg_match('/<title[^>]*>(.*?)<\/title>/is', $svgContent, $titleMatches)) {
          $result['result'] = false;
          $result['errors'][] = 'Invalid SVG: Missing required <title> element.';
        } else {
          // Check for content in <title> (assuming it reflects company name)
          if (empty(trim($titleMatches[1]))) {
            $result['result'] = false;
            $result['errors'][] = 'Invalid SVG: <title> element should contain company name.';
          }
        }
      
        // Presence of <desc> is not mandatory but recommended
        if (!preg_match('/<desc[^>]*>(.*?)<\/desc>/is', $svgContent)) {
          $result['result'] = false;
          $result['errors'][] = 'Invalid SVG: <desc> element is not present for accessibility.';
        }
      
        // Check for square aspect ratio (basic check)
        if (preg_match('/<svg\s+[^>]*?(width|height)\s*=\s*(["\'])(\d+)(["\'])/i', $svgContent, $matches)) {
          if (isset($matches[3]) && isset($matches[5]) && $matches[3] != $matches[5]) {
            $result['result'] = false;
            $result['errors'][] = 'Invalid SVG: Image is not a square aspect ratio (width and height should be equal).';
          }
        }
      
        // File size check (basic - can be improved for accuracy)
        if (strlen($svgContent) > 32 * 1024) {
          $result['result'] = false; // Can be a separate key for warnings
          $result['errors'][] = 'Invalid SVG: SVG file size exceeds recommended limit of 32 KB.';
        }

        // Check for external links or references
        if (preg_match('/<\w+\s+[^>]*?(href|xlink:href)\s*=\s*(["\'])[^>]*?(["\'])/i', $svgContent)) {
            $result['result'] = false;
            $result['errors'][] = 'Invalid SVG: External links or references are not allowed.';
        }
        // Check for scripts
        if (preg_match('/<script|<\/script>/is', $svgContent)) {
            $result['result'] = false;
            $result['errors'][] = 'Invalid SVG: Scripts are not allowed.';
        }
        // Check for animation elements (consider adding more as needed)
        if (preg_match('/<animate|<\/animate>/is', $svgContent)) {
            $result['result'] = false;
            $result['errors'][] = 'Invalid SVG: Animations are not allowed.';
        }
        // Check for x and y attributes on the <svg> element
        if (preg_match('/<svg\s+[^>]*?\W(x|y)\s*=\s*["\'][^>]*?["\']/i', $svgContent)) {
            $result['result'] = false;
            $result['errors'][] = 'Invalid SVG: "x" and "y" attributes are not allowed on the <svg> element.';
        }

        return $result;
      }

    public function findOwnedBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
    {
        $domains = array();
        foreach ($criteria as $criterion) {
            if(is_int($criterion)) {
                $domains[] = $criterion;
            }
        }
        $qb = $this->createQueryBuilder('d');
        if(!empty($domains)) {
            $qb->andWhere('d.id IN (:domains)')
            ->setParameter('domains', $domains);
        }
        foreach($orderBy as $key => $value) {
            $qb->addOrderBy('d.'.$key, $value);
        }
        if(!empty($limit)) {
            $qb->setMaxResults($limit);
        }
        if(!empty($offset)) {
            $qb->setFirstResult($offset);
        }
        return $qb->getQuery()->getResult();
    }

    public function findFormSelectedDomains($options): array
    {
        if(array_key_exists('data', $options) && $options["data"]->getId() != null) {
            return $this->findSelectedDomains($options["data"]->getId());
        } else {
            return array();
        }
    }

    public function findSelectedDomains($user_id): array
    {
        $ids = array();
        $user = $this->usersRepository->find($user_id);
        foreach($user->getDomains() as $domain) {
            $ids[] = $domain->getId();
        }

        return $this->createQueryBuilder('d')
            ->andWhere("d.id IN (:ids)")
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getTotalRows(): int
    {
        return $this->createQueryBuilder('d')
            ->select('count(d.id)')
            ->getQuery()
            ->getOneOrNullResult()[1]
        ;
    }
}
