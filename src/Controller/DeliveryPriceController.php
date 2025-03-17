<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\DeliveryPrice;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use App\Helper\NormalizableExceptionHandlerTrait; 

#[Route("/deliveryprice")]
class DeliveryPriceController extends AbstractController
{
    use NormalizableExceptionHandlerTrait;
    private $entityManagerInterface;

    public function __construct(EntityManagerInterface $entityManagerInterface){
        $this->entityManagerInterface=$entityManagerInterface;
    }
    #[Route('/all', name: 'api_delivery_price_all',methods:["GET"])]

    public function getAll(): JsonResponse
    {
        $deliveryPrice=$this->entityManagerInterface->getRepository(DeliveryPrice::class)->findAll();
        return $this->json($deliveryPrice,200,[],["groups"=>"deliveryprice:read"]);
   
    }

    #[Route('/{id}', name: 'api_delivery_price_by_id',methods:["GET"])]

    public function getById(deliveryPrice $deliveryPrice=null): JsonResponse
    {
        return $this->json($deliveryPrice,200,[],["groups"=>"deliveryprice:read"]);
    }

    #[Route('/create', name: 'api_delivery_price_create',methods:["POST"])]

    public function create(ValidatorInterface $validator, SerializerInterface $serializer, Request $request): JsonResponse
    {
        try {
            $jsonData = $request->getContent();
            $deliveryPrice = $serializer->deserialize($jsonData, DeliveryPrice::class, 'json');
            
            $errors = $validator->validate($deliveryPrice);
            if (count($errors) > 0) {
                $firstError = $errors[0];
                $propertyPath = $firstError->getPropertyPath();
                $title = $firstError->getMessage();
                return $this->json(["message" => $title], 400);
            }
            
            $this->entityManagerInterface->persist($deliveryPrice);
            $this->entityManagerInterface->flush();
    
            return $this->json(['message' => 'deliveryPrice created successfully', 'color' => $deliveryPrice], 201, [], ["groups" => "deliveryprice:read"]);
        } catch (NotNormalizableValueException $e) {
            return $this->handleNormalizableValueException($e);
              
        } catch (NotEncodableValueException $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

#[Route("/update/{id}",name:"api_delivery_price_update",methods:["PUT"])]
public function update(SerializerInterface $serializer,deliveryPrice $deliveryPrice=null,Request $request):JsonResponse{
    if(!$deliveryPrice){
        return $this->json(["message"=>"deliveryPrice not found"],404);
    }
    else {
    try{
    $jsonData=$request->getContent();
    $data = $serializer->deserialize($jsonData, DeliveryPrice::class, 'json');
    if($data->getName()!=null){
        $deliveryPrice->setName($data->getName());
    }

    if($data->getPrice()!=null){
        $deliveryPrice->setPrice($data->getPrice());
    }
    $this->entityManagerInterface->flush();
    return $this->json(["message"=>"deliveryPrice updated","deliveryPrice"=>$deliveryPrice],200,[],["groups"=>"deliveryprice:read"]);
    
    } catch (NotNormalizableValueException $e) {
    return $this->handleNormalizableValueException($e);

    }catch(NotEncodableValueException $e){
        return $this->json(['message' =>$e->getMessage()], 400);
    }
}}

#[Route("/delete/{id}",name:"api_delivery_price_delete",methods:["DELETE"])]
public function delete(deliveryPrice $deliveryPrice=null):JsonResponse{

    if(!$deliveryPrice){
        return $this->json(["message"=>"deliveryPrice not found"],400);
    }
    else {
    try{
    $this->entityManagerInterface->remove($deliveryPrice);
    $this->entityManagerInterface->flush();
    return $this->json(["message"=>"deliveryPrice deleted"],200);
    }catch(NotEncodableValueException $e){
        return $this->json(['message' =>$e->getMessage()], 400);
    }}
}

}