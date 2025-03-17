<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Reduction;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use App\Helper\NormalizableExceptionHandlerTrait; 

#[Route("/reduction")]
class ReductionController extends AbstractController
{
    use NormalizableExceptionHandlerTrait;
    private $entityManagerInterface;

    public function __construct(EntityManagerInterface $entityManagerInterface){
        $this->entityManagerInterface=$entityManagerInterface;
    }
    #[Route('/all', name: 'api_reduction_all',methods:["GET"])]

    public function getAll(): JsonResponse
    {
        $reduction=$this->entityManagerInterface->getRepository(Reduction::class)->findAll();
        return $this->json($reduction,200,[],["groups"=>"reduction:read"]);
   
    }

    #[Route('/{id}', name: 'api_reduction_by_id',methods:["GET"])]

    public function getById(Reduction $reduction=null): JsonResponse
    {
        return $this->json($reduction,200,[],["groups"=>"reduction:read"]);
    }

    #[Route('/create', name: 'api_reduction_create',methods:["POST"])]

    public function create(ValidatorInterface $validator, SerializerInterface $serializer, Request $request): JsonResponse
    {
        try {
            $jsonData = $request->getContent();
            $reduction = $serializer->deserialize($jsonData, Reduction::class, 'json');
            
            $errors = $validator->validate($reduction);
            if (count($errors) > 0) {
                $firstError = $errors[0];
                $propertyPath = $firstError->getPropertyPath();
                $title = $firstError->getMessage();
                return $this->json(["message" => $title], 400);
            }
            
            $this->entityManagerInterface->persist($reduction);
            $this->entityManagerInterface->flush();
    
            return $this->json(['message' => 'Reduction created successfully', 'reduction' => $reduction], 201, [], ["groups" => "reduction:read"]);
        } catch (NotNormalizableValueException $e) {
            return $this->handleNormalizableValueException($e);
              
        } catch (NotEncodableValueException $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

#[Route("/update/{id}",name:"api_reduction_update",methods:["PUT"])]
public function update(SerializerInterface $serializer,Reduction $reduction=null,Request $request):JsonResponse{
    if(!$reduction){
        return $this->json(["message"=>"reduction not found"],404);
    }
    else {
    try{
    $jsonData=$request->getContent();
    $data = $serializer->deserialize($jsonData, Reduction::class, 'json');
    if($data->getName()!=null){
        $reduction->setName($data->getName());
    }

    if($data->getReductionVal()!=null){
        $reduction->setReductionVal($data->getReductionVal());
    }
    $this->entityManagerInterface->flush();
    return $this->json(["message"=>"reduction updated","reduction"=>$reduction],200,[],["groups"=>"reduction:read"]);
    
    } catch (NotNormalizableValueException $e) {
    return $this->handleNormalizableValueException($e);

    }catch(NotEncodableValueException $e){
        return $this->json(['message' =>$e->getMessage()], 400);
    }
}}

#[Route("/delete/{id}",name:"api_reduction_delete",methods:["DELETE"])]
public function delete(Reduction $reduction=null):JsonResponse{

    if(!$reduction){
        return $this->json(["message"=>"reduction not found"],400);
    }
    else {
    try{
    $this->entityManagerInterface->remove($reduction);
    $this->entityManagerInterface->flush();
    return $this->json(["message"=>"reduction deleted"],200);
    }catch(NotEncodableValueException $e){
        return $this->json(['message' =>$e->getMessage()], 400);
    }}
}

}