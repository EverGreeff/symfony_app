<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductRepository;
use App\Entity\Product;
use App\Form\ProductType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

final class ProductController extends AbstractController
{
    #[Route('/products', name: 'product_index')]
    public function index(ProductRepository $repository): Response
    {
        //$products = $repository->findAll();

        //dd($products); //Dump and Die

        return $this->render('product/index.html.twig', [
            'products' => $repository->findAll(),
        ]);
    }

    #[Route('/product/{id<\d+>}', name: 'product_show')]
    public function show(Product $product, ProductRepository $repository): Response
    {
        #$product = $repository->findOneBy(['id' => $id]);

        #if($product === null) {
        #    throw $this->createNotFoundException('Product not found');
        #}

        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }

    #[Route('product/new', name: 'product_new')]
    public function new(Request $request, EntityManagerInterface $manager): Response 
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //dd($request->request->all());
            $manager->persist($product);

            $manager->flush();

            $this->addFlash('notice','Created successfully!');

            return $this->redirectToRoute('product_show', [
                'id'=> $product->getId()
            ]);
        }
        
        /*
        if ($_SERVER['REQUEST_METHOD'] === 'POST') 
        {
                dd($_POST);
        }
        */

        return $this->render('product/new.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/product/{id<\d+>}/edit', name: 'product_edit')]
    public function edit(Product $product, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->flush();

            $this->addFlash('notice','Updated successfully!');

            return $this->redirectToRoute('product_show', [
                'id'=> $product->getId()
            ]);
        }
        
        /*
        if ($_SERVER['REQUEST_METHOD'] === 'POST') 
        {
                dd($_POST);
        }
        */

        return $this->render('product/edit.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/product/{id<\d+>}/delete', name: 'product_delete')]
    public function delete(Request $request, Product $product, EntityManagerInterface $manager) : Response 
    {
        if($request->isMethod('POST')) 
        {
            $manager->remove($product);
            $manager->flush();
            $this->addFlash('notice','Product deleted!');

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/delete.html.twig', [
            'id' => $product->getId(),
        ]);
    }
}
