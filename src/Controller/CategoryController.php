<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Category;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route("/category")]
class CategoryController extends AbstractController
{

    private $entityManagerInterface;

    public function __construct(EntityManagerInterface $entityManagerInterface){
        $this->entityManagerInterface=$entityManagerInterface;
    }
    #[Route('/all', name: 'api_category_all',methods:["GET"])]

    public function getAll(): JsonResponse
    {
        $category=$this->entityManagerInterface->getRepository(Category::class)->findAll();
        return $this->json($category,200,[],["groups"=>"category:read"]);
    }

    #[Route('/{id}', name: 'api_category_by_id',methods:["GET"])]

    public function getById(Category $category=null): JsonResponse
    {
        return $this->json($category,200,[],["groups"=>"category:read"]);
    }

    #[Route('/create', name: 'api_category_create',methods:["POST"])]

    public function create(ValidatorInterface $validator,SerializerInterface $serializer,Request $request): JsonResponse
    {
        try{
        $jsonData=$request->getContent();
        $category=$serializer->deserialize($jsonData,Category::class,'json');
        $errors=$validator->validate($category);
        if(count($errors)>0){
            $firstError = $errors[0];
            $propertyPath = $firstError->getPropertyPath();
            $title = $firstError->getMessage();
            return $this->json(["message"=>$title], 400);

        }
        else {
        $this->entityManagerInterface->persist($category);
        $this->entityManagerInterface->flush();

        return $this->json(['message' => 'Category created successfully','category'=>$category], 201,[],["groups" => "category:read"]);
    }}
    catch(NotEncodableValueException $e){
        return $this->json(['message' =>$e->getMessage()], 400);

    }
}

#[Route("/update/{id}",name:"api_category_update",methods:["PUT"])]
public function update(SerializerInterface $serializer,Category $category=null,Request $request):JsonResponse{
    if(!$category){
        return $this->json(["message"=>"Category not found"],404);
    }
    else {
    try{
    $jsonData=$request->getContent();
    $data = $serializer->deserialize($jsonData, Category::class, 'json');
    if($data->getName()!=null){
        $category->setName($data->getName());
    }
    $this->entityManagerInterface->flush();
    return $this->json(["message"=>"Category updated","category"=>$category],200,[],["groups"=>"category:read"]);
    
    }catch(NotEncodableValueException $e){
        return $this->json(['message' =>$e->getMessage()], 400);
    }
}}

#[Route("/delete/{id}",name:"api_category_delete",methods:["DELETE"])]
public function delete(Category $category=null):JsonResponse{

    if(!$category){
        return $this->json(["message"=>"Categoy not found"],400);
    }
    else {
    try{
    $this->entityManagerInterface->remove($category);
    $this->entityManagerInterface->flush();
    return $this->json(["message"=>"Category deleted"],200);
    }catch(NotEncodableValueException $e){
        return $this->json(['message' =>$e->getMessage()], 400);
    }}
}

}