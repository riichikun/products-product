<?php
/*
 *  Copyright 2024.  Baks.dev <admin@baks.dev>
 *  
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *  
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *  
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

namespace BaksDev\Products\Product\Controller;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Repository\SettingsMain\SettingsMainInterface;
use BaksDev\Core\Type\UidType\ParamConverter;
use BaksDev\Products\Category\Repository\AllCategoryByMenu\AllCategoryByMenuInterface;
use BaksDev\Products\Category\Type\Id\CategoryProductUid;
use BaksDev\Products\Product\Repository\AllProductsByCategory\AllProductsByCategoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class ExportController extends AbstractController
{
    #[Route('/export.xml', name: 'export', methods: ['GET'])]
    public function search(
        Request $request,
        SettingsMainInterface $settingsMain,
        AllCategoryByMenuInterface $allCategory,
        AllProductsByCategoryInterface $productsByCategory
    ): Response
    {

        $response = $this->render(
            [
                'category' => $allCategory->findAll(),
                'settings' => $settingsMain->getSettingsMainAssociative($request->getHost(), $request->getLocale()),
                'products' => $productsByCategory->fetchAllProductByCategory()],
            file: 'export.html.twig'
        );

        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }


    #[Route('/catalog.xml', name: 'export.catalog', methods: ['GET'])]
    public function catalog(
        Request $request,
        SettingsMainInterface $settingsMain,
        AllCategoryByMenuInterface $allCategory,
        AllProductsByCategoryInterface $productsByCategory
    ): Response
    {

        $response = $this->render(
            [
                'settings' => $settingsMain->getSettingsMainAssociative($request->getHost(), $request->getLocale()),
                'category' => $allCategory->findAll(),
                'products' => $productsByCategory->fetchAllProductByCategory()],
            file: 'catalog.html.twig'
        );
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }


    #[Route('/terms.xml', name: 'export.terms', methods: ['GET'])]
    public function terms(
        Request $request,
        SettingsMainInterface $settingsMain,
        AllProductsByCategoryInterface $productsByCategory
    ): Response
    {

        $response = $this->render(
            [
                'settings' => $settingsMain->getSettingsMainAssociative($request->getHost(), $request->getLocale()),
                'products' => $productsByCategory->fetchAllProductByCategory()],
            file: 'terms.html.twig'
        );
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }


    #[Route('/export.json', name: 'export.json', methods: ['GET'])]
    public function export(
        #[ParamConverter(CategoryProductUid::class)] $category,
        AllProductsByCategoryInterface $productsByCategory
    ): Response
    {

        $response = $this->render(['urls' => $productsByCategory->fetchAllProductByCategory($category)]);
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }

}
