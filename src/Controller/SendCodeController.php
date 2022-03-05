<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Codes;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Rossana Pencheva <rossana.ruseva@gmail.com>
 */
class SendCodeController extends AbstractController
{
     /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/api/send/code", name="app_send_code", methods={"POST"})
     */
    public function requestSendCode(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        if(!isset($content['phone']) && !$content['phone']){
            throw new NotFoundHttpException('Phone not found.');
        }
        $phone = $content['phone'];    
        
         //check if phone was already verified  
        $isVerified = $this->entityManager
            ->getRepository(Codes::class)
            ->findOneBy(['success' => true, 'phone' => $phone]);
        if($isVerified){
            return new JsonResponse(['check' => $isVerified], JsonResponse::HTTP_OK);
        }    

        //find valid code from less than one minute
        $validCodeFromOneMinute = $this->entityManager
            ->getRepository(Codes::class)
            ->findValidFromOneMinuteAgo($phone);   
        if($validCodeFromOneMinute){
            return new JsonResponse(['warning' => "А code has already been sent to your phone."], 
            JsonResponse::HTTP_OK);
        }else{
            //if old codes are still valid make then unvalid
            $validCodes = $this->entityManager
                ->getRepository(Codes::class)
                ->findBy(['valid' => true, 'phone' => $phone]);
            foreach($validCodes as $validCode){
                $validCode->setValid(false);
                $this->entityManager->persist($validCode);
                $this->entityManager->flush();
            }    
            //send code to phone
            $this->sendCode($phone);
        }            
        
        return new JsonResponse(['message' => "We've sent a verification code to your phone."], 
        JsonResponse::HTTP_OK);
    }

    protected function sendCode($phone)
    {
        $code = $this->generateNewCode();
        $codeSended = new Codes($phone, $code); 
        $this->entityManager->persist($codeSended);
        $this->entityManager->flush();
    }

    protected function generateNewCode()
    {
        $randomNumber = mt_rand(100000, 999999);
        return $randomNumber;
    }

}