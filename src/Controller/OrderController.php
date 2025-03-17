<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Order;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use App\Helper\NormalizableExceptionHandlerTrait; 

#[Route("/order")]
class OrderController extends AbstractController
{
    use NormalizableExceptionHandlerTrait;
    private $entityManagerInterface;

    public function __construct(EntityManagerInterface $entityManagerInterface){
        $this->entityManagerInterface=$entityManagerInterface;
    }
    #[Route('/all', name: 'api_order_all',methods:["GET"])]

    public function getAll(): JsonResponse
    {
        $order=$this->entityManagerInterface->getRepository(Order::class)->findAll();
        return $this->json($order,200,[],["groups"=>"order:read"]);
   
    }

    #[Route('/{id}', name: 'api_order_by_id',methods:["GET"])]

    public function getById(Order $order=null): JsonResponse
    {
        return $this->json($order,200,[],["groups"=>"order:read"]);
    }

    #[Route('/create', name: 'api_order_create',methods:["POST"])]

    public function create(ValidatorInterface $validator, SerializerInterface $serializer, Request $request): JsonResponse
    {
        try {
            $jsonData = $request->getContent();
            $order = $serializer->deserialize($jsonData, Order::class, 'json');
            
            $errors = $validator->validate($order);
            if (count($errors) > 0) {
                $firstError = $errors[0];
                $propertyPath = $firstError->getPropertyPath();
                $title = $firstError->getMessage();
                return $this->json(["message" => $title], 400);
            }
            
            $this->entityManagerInterface->persist($order);
            $this->entityManagerInterface->flush();
    
            return $this->json(['message' => 'deliveryPrice created successfully', 'reduction' => $order], 201, [], ["groups" => "order:read"]);
        } catch (NotNormalizableValueException $e) {
            return $this->handleNormalizableValueException($e);
              
        } catch (NotEncodableValueException $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

#[Route("/update/{id}",name:"api_order_update",methods:["PUT"])]
public function update(SerializerInterface $serializer,deliveryPrice $order=null,Request $request):JsonResponse{
    if(!$order){
        return $this->json(["message"=>"order not found"],404);
    }
    else {
    try{
    $jsonData=$request->getContent();
    $data = $serializer->deserialize($jsonData, Order::class, 'json');
    if($data->getName()!=null){
        $order->setName($data->getName());
    }

    if($data->getPrice()!=null){
        $order->setPrice($data->getPrice());
    }
    $this->entityManagerInterface->flush();
    return $this->json(["message"=>"order updated","order"=>$order],200,[],["groups"=>"order:read"]);
    
    } catch (NotNormalizableValueException $e) {
    return $this->handleNormalizableValueException($e);

    }catch(NotEncodableValueException $e){
        return $this->json(['message' =>$e->getMessage()], 400);
    }
}}

#[Route("/delete/{id}",name:"api_order_delete",methods:["DELETE"])]
public function delete(Order $order=null):JsonResponse{

    if(!$order){
        return $this->json(["message"=>"order not found"],400);
    }
    else {
    try{
    $this->entityManagerInterface->remove($order);
    $this->entityManagerInterface->flush();
    return $this->json(["message"=>"order deleted"],200);
    }catch(NotEncodableValueException $e){
        return $this->json(['message' =>$e->getMessage()], 400);
    }}
}

}