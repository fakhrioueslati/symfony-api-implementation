<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Role;
use App\Entity\Permission;
use App\Entity\Brand;
use App\Entity\Order;
use App\Entity\OrderStatus;
use App\Entity\Color;
use App\Entity\Size;
use App\Entity\Category;
use App\Entity\DeliveryPrice;
use App\Entity\Product;
use App\Entity\Reduction;
use Symfony\Component\Uid\Ulid;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        
        $roleArray=array("user","admin","seller");
        $permissionArray=array("create","update","delete");        
        for($i=0;$i<count($roleArray);$i++){

        $role=new Role();
        $role->setName($roleArray[$i]);
        $role->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($role);
        //$role->addPermission($permission);
        }

        for($i=0;$i<count($permissionArray);$i++){

        $permission=new Permission();
        $permission->setName($permissionArray[$i]);
        $permission->setCreatedAt(new \DateTimeImmutable());

        $manager->persist($permission);
        }
    
        for($i=0;$i<5;$i++){
            $orderStatus=new OrderStatus();
            $orderStatus->setName("orderStatus $i");
            $orderStatus->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($orderStatus);

            for($l=0;$l<5;$l++){
                $delivery=new DeliveryPrice();
                $delivery->setName("delivery $i");
                $delivery->setPrice($i);
                $delivery->setCreatedAt(new \DateTimeImmutable());
                $manager->persist($delivery);
            

            for($j=0;$j<5;$j++){
                $order=new Order();
                $order->setOrderNumber(Ulid::fromString(Ulid::generate()));
                $order->setStatus($orderStatus);
                $order->setTotal($i+$j);
                $order->setDelivryPrice($delivery);
                $order->setCreatedAt(new \DateTimeImmutable());
                $manager->persist($order);
            }}

            for($i=0;$i<5;$i++){
                $category=new Category();
                $category->setName("category $i");
                $category->setCreatedAt(new \DateTimeImmutable());
                $manager->persist($category);
            
            for($l=0;$l<5;$l++){
                $size=new Size();
                $size->setName("size $l");
                $size->setCreatedAt(new \DateTimeImmutable());
                $manager->persist($size);

                for($s=0;$s<5;$s++){
                    $brand=new Brand();
                    $brand->setName("brand $s");
                    $brand->setCreatedAt(new \DateTimeImmutable());
                    $manager->persist($brand);
                

                for($b=0;$b<5;$b++){
                    $color=new Color();
                    $color->setName("color $b");
                    $color->setCreatedAt(new \DateTimeImmutable());
                    $manager->persist($color);
                
            for($b=0;$b<5;$b++){
                $reduction=new Reduction();
                $reduction->setName("reduction $b");
                $reduction->setReductionVal($b*2);
                $reduction->setCreatedAt(new \DateTimeImmutable());
                $manager->persist($reduction);

            for($j=0;$j<5;$j++){
                $product=new Product();
                $product->setName("product $j");
                $product->setDescription("description $j");
                $product->setQuantity($j);
                $product->setPrice($j*4);
                $product->setImage("https://placehold.co/600x400");
                $product->setOnCover(false);
                $product->setCategory($category);
                $product->setSize(array($size->getId()));
                $product->setBrand($brand);
                $product->setColor(array($color->getId()));
                $product->setShowHide(true);
                $product->setReduction($reduction);

                $product->setCreatedAt(new \DateTimeImmutable());
                $manager->persist($product);
            }}}}}}

        }

        $manager->flush();

    }
}
