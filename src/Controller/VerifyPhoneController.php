<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Codes;
use App\Entity\Attempts;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class VerifyPhoneController extends AbstractController
{
     /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

      /**
     * @Route("/api/check/phone", name="app_check_phone", methods={"POST"})
     */
    public function checkPhone(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        
        if(!isset($content['phone']) && !$content['phone']){
            throw new NotFoundHttpException('Phone not found.');
        }
        $phone = $content['phone'];

        $isVerified = $this->isVerified($phone);
        
        return new JsonResponse(['check' => $isVerified], JsonResponse::HTTP_OK);

    }

    public function isVerified($phone){
        $isVerified = $this->entityManager
            ->getRepository(Codes::class)
            ->findOneBy(['success' => true, 'phone' => $phone]);
        if($isVerified){
            return true;
        }else{
            return false;
        }
    }
   
    /**
     * @Route("/api/verify/phone", name="app_verify_phone", methods={"POST"})
     */
    public function verifyPhone(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        
        if(!isset($content['phone']) && !$content['phone']){
            throw new NotFoundHttpException('Phone not found.');
        }
        $phone = $content['phone'];
        $check = $this->isVerified($phone);

        if($check){
            return new JsonResponse(['message' => "Your phone has been already validated."], JsonResponse::HTTP_OK);
        }
        
        if(!isset($content['code']) && !$content['code']){
            throw new NotFoundHttpException('Code not found.');
        }
        $code = $content['code'];
        //does code muach
        $verifiedCode = $this->entityManager
                ->getRepository(Codes::class)
                ->findOneBy(['valid' => true, 'phone' => $phone, 'code' => $code]);        
                
        $attempt = new Attempts($phone, $code);
        if($verifiedCode){
            $attempt->setCodeId($verifiedCode);
            $attempt->setSuccess(true);
            $verifiedCode->setSuccess(true);
            $this->entityManager->persist($verifiedCode);
            $this->entityManager->flush();
            return new JsonResponse(['message' => "Your phone has been successfully validated."], 
        JsonResponse::HTTP_OK);
        }else{
            $validCode = $this->entityManager
                ->getRepository(Codes::class)
                ->findOneBy(['valid' => true, 'phone' => $phone]);
            if($validCode){
                $attempt->setCodeId($validCode);
            }            
            $attempt->setSuccess(false);
            $this->entityManager->persist($attempt);
            $this->entityManager->flush();
            return new JsonResponse(['error' => "Failure! The code does not match"], 
        JsonResponse::HTTP_BAD_REQUEST);
        }
       
    }

}