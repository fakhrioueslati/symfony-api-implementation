<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Color;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route("/color")]
class ColorController extends AbstractController
{

    private $entityManagerInterface;

    public function __construct(EntityManagerInterface $entityManagerInterface){
        $this->entityManagerInterface=$entityManagerInterface;
    }
    #[Route('/all', name: 'api_color_all',methods:["GET"])]

    public function getAll(): JsonResponse
    {
        $color=$this->entityManagerInterface->getRepository(Color::class)->findAll();
        return $this->json($color,200,[],["groups"=>"color:read"]);
   
    }

    #[Route('/{id}', name: 'api_color_by_id',methods:["GET"])]

    public function getById(Color $color=null): JsonResponse
    {
        return $this->json($color,200,[],["groups"=>"color:read"]);
    }

    #[Route('/create', name: 'api_color_create',methods:["POST"])]

    public function create(ValidatorInterface $validator,SerializerInterface $serializer,Request $request): JsonResponse
    {
        try{
        $jsonData=$request->getContent();
        $color=$serializer->deserialize($jsonData,Color::class,'json');
        $errors=$validator->validate($color);
        if(count($errors)>0){
            $firstError = $errors[0];
            $propertyPath = $firstError->getPropertyPath();
            $title = $firstError->getMessage();
            return $this->json(["message"=>$title], 400);

        }
        else {
        $this->entityManagerInterface->persist($color);
        $this->entityManagerInterface->flush();

        return $this->json(['message' => 'Color created successfully','color'=>$color], 201,[],["groups" => "color:read"]);
    }}
    catch(NotEncodableValueException $e){
        return $this->json(['message' =>$e->getMessage()], 400);

    }
}

#[Route("/update/{id}",name:"api_color_update",methods:["PUT"])]
public function update(SerializerInterface $serializer,Color $color=null,Request $request):JsonResponse{
    if(!$color){
        return $this->json(["message"=>"Color not found"],404);
    }
    else {
    try{
    $jsonData=$request->getContent();
    $data = $serializer->deserialize($jsonData, Color::class, 'json');
    if($data->getName()!=null){
        $color->setName($data->getName());
    }
    $this->entityManagerInterface->flush();
    return $this->json(["message"=>"Color updated","color"=>$color],200,[],["groups"=>"color:read"]);
    
    }catch(NotEncodableValueException $e){
        return $this->json(['message' =>$e->getMessage()], 400);
    }
}}

#[Route("/delete/{id}",name:"api_color_delete",methods:["DELETE"])]
public function delete(Color $color=null):JsonResponse{

    if(!$color){
        return $this->json(["message"=>"Color not found"],400);
    }
    else {
    try{
    $this->entityManagerInterface->remove($color);
    $this->entityManagerInterface->flush();
    return $this->json(["message"=>"Color deleted"],200);
    }catch(NotEncodableValueException $e){
        return $this->json(['message' =>$e->getMessage()], 400);
    }}
}

}