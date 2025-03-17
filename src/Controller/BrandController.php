<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Brand;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/brand')]
class BrandController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine){
        $this->doctrine=$doctrine;
    }
    #[Route('/all', name: 'api_brand_all', methods: ["GET"])]

    public function getAll(): JsonResponse
    {
        $brand = $this->doctrine->getRepository(Brand::class)->findAll();
        
        return $this->json($brand, 200, [], ["groups" => "brand:read"]);
    }

    #[Route('/{id}', name: 'api_brand_by_id', methods: ["GET"])]

    public function getByid(Brand $brand=null): JsonResponse
    {
        
        return $this->json($brand, 200, [], ["groups" => "brand:read"]);
    }
    
    #[Route('/create', name: 'api_brand_create', methods: ["POST"])]
    
    public function createBrand(ValidatorInterface $validator,Request $request, SerializerInterface $serializer):JsonResponse
    {
        try{
        $jsondata=$request->getContent();
        $brand = $serializer->deserialize($jsondata, Brand::class, 'json');
        $errors = $validator->validate($brand);
        if(count($errors)>0){
            $firstError = $errors[0];
            $propertyPath = $firstError->getPropertyPath();
            $title = $firstError->getMessage();
            return $this->json(["message"=>$title], 400);

        }
        else {

        
        $entityManager=$this->doctrine->getManager();
        $entityManager->persist($brand);
        $entityManager->flush();
        
        return $this->json(['message' => 'Brand created successfully','brand'=>$brand], 201,[],["groups" => "brand:read"]);
        }}
        catch (NotEncodableValueException $e) {
            return $this->json([
                'message' => $e->getMessage()
            ],400);
        }
    }

    #[Route('/update/{id}', name: 'api_brand_update', methods: ["PUT"])]
    
    public function updateBrand(SerializerInterface $serializer,Brand $brand=null,Request $request):JsonResponse
    {
        if(!$brand){
            return $this->json(['message' => 'Brand not found'], 404);
        }
        else {
            try{
            $jsondata=$request->getContent();
            $data=$serializer->deserialize($jsondata,Brand::class,"json");
            if ($data->getName() !== null) {
                $brand->setName($data->getName());
            }
    
            if ($data->getDescription() !== null) {
                $brand->setDescription($data->getDescription());
            }
            $entityManager=$this->doctrine->getManager();
            $entityManager->flush();
            return $this->json(['message' => 'Brand updated','brand'=>$brand], 200,[],["groups" => "brand:read"]);
            }catch(NotEncodableValueException $e){
                return $this->json(['message' =>$e->getMessage()], 400);

            }
        }
        

    }

    #[Route('/delete/{id}', name: 'api_brand_delete', methods: ["DELETE"])]
    public function delete(Brand $brand=null) :JsonResponse{

        if(!$brand){
            return $this->json(['message' =>"brand not found"], 404);

        }
        else {
            try{
            $entityManager=$this->doctrine->getManager();
            $entityManager->remove($brand);
            $entityManager->flush();
            return $this->json(['message' =>"brand delete"], 200);

        }catch(NotEncodableValueException $e){
            return $this->json(['message' =>$e->getMessage()], 400);

        }}

    }
    
}
