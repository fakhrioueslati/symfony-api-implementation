<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Size;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use App\Helper\NormalizableExceptionHandlerTrait; 

#[Route("/size")]
class SizeController extends AbstractController
{
    use NormalizableExceptionHandlerTrait;
    private $entityManagerInterface;

    public function __construct(EntityManagerInterface $entityManagerInterface){
        $this->entityManagerInterface=$entityManagerInterface;
    }
    #[Route('/all', name: 'api_size_all',methods:["GET"])]

    public function getAll(): JsonResponse
    {
        $size=$this->entityManagerInterface->getRepository(Size::class)->findAll();
        return $this->json($size,200,[],["groups"=>"size:read"]);
   
    }

    #[Route('/{id}', name: 'api_size_by_id',methods:["GET"])]

    public function getById(Size $size=null): JsonResponse
    {
        return $this->json($size,200,[],["groups"=>"size:read"]);
    }

    #[Route('/create', name: 'api_size_create',methods:["POST"])]

    public function create(ValidatorInterface $validator, SerializerInterface $serializer, Request $request): JsonResponse
    {
        try {
            $jsonData = $request->getContent();
            $size = $serializer->deserialize($jsonData, Size::class, 'json');
            
            $errors = $validator->validate($size);
            if (count($errors) > 0) {
                $firstError = $errors[0];
                $propertyPath = $firstError->getPropertyPath();
                $title = $firstError->getMessage();
                return $this->json(["message" => $title], 400);
            }
            
            $this->entityManagerInterface->persist($size);
            $this->entityManagerInterface->flush();
    
            return $this->json(['message' => 'Size created successfully', 'size' => $size], 201, [], ["groups" => "size:read"]);
        } catch (NotNormalizableValueException $e) {
            return $this->handleNormalizableValueException($e);
              
        } catch (NotEncodableValueException $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

#[Route("/update/{id}",name:"api_size_update",methods:["PUT"])]
public function update(SerializerInterface $serializer,Size $size=null,Request $request):JsonResponse{
    if(!$size){
        return $this->json(["message"=>"size not found"],404);
    }
    else {
    try{
    $jsonData=$request->getContent();
    $data = $serializer->deserialize($jsonData, Size::class, 'json');
    if($data->getName()!=null){
        $size->setName($data->getName());
    }

    $this->entityManagerInterface->flush();
    return $this->json(["message"=>"size updated","size"=>$size],200,[],["groups"=>"size:read"]);
    
    } catch (NotNormalizableValueException $e) {
    return $this->handleNormalizableValueException($e);

    }catch(NotEncodableValueException $e){
        return $this->json(['message' =>$e->getMessage()], 400);
    }
}}

#[Route("/delete/{id}",name:"api_size_delete",methods:["DELETE"])]
public function delete(Size $size=null):JsonResponse{

    if(!$size){
        return $this->json(["message"=>"size not found"],400);
    }
    else {
    try{
    $this->entityManagerInterface->remove($size);
    $this->entityManagerInterface->flush();
    return $this->json(["message"=>"size deleted"],200);
    }catch(NotEncodableValueException $e){
        return $this->json(['message' =>$e->getMessage()], 400);
    }}
}

}