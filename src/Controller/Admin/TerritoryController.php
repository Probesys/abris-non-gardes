<?php

namespace App\Controller\Admin;

use App\Entity\City;
use App\Entity\Territory;
use App\Form\TerritoryType;
use App\FormFilter\CityFilterType;
use App\FormFilter\TerritoryFilterType;
use App\Repository\CityRepository;
use App\Repository\NestedTerritoryRepository;
use App\Repository\TerritoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Territory controller.
 *
 * @Route("/admin/territory")
 */
class TerritoryController extends AbstractController
{
    /**
     * Lists all Territory entities.
     *
     * @Route("/", name="territory_index")
     *
     * @Method({"GET", "POST"})
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $filterData = [];
        $filterForm = $this->createForm(TerritoryFilterType::class);

        // Reset filter
        if ('reset' == $request->get('filter_action')) {
            $session->remove('territoryFilter');

            return $this->redirect($this->generateUrl('territory_index'));
        }

        // Filter action
        if ('filter' == $request->get('filter_action')) {
            // Bind values from the request
            $filterForm->handleRequest($request);

            if ($filterForm->isValid()) {
                // Save filter to session
                if ($filterForm->has('name')) {
                    $filterData['name'] = $filterForm->get('name')->getData();
                }

                $session->set('territoryFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('territoryFilter')) {
                $filterData = $session->get('territoryFilter');
                $filterForm = $this->createForm(TerritoryType::class);
                if (array_key_exists('name', $filterData)) {
                    $filterForm->get('name')->setData($filterData['name']);
                }
            }
        }
        $territoryRepository = $em->getRepository(Territory::class);

        $query = $territoryRepository->search($filterData);

        $options = ['decorate' => false];

        $htmlTree = $territoryRepository->buildTree($query->getArrayResult(), $options);

        return $this->render('admin/territory/index.html.twig', ['search_form' => $filterForm->createView(), 'htmlTree' => $htmlTree, 'nbResults' => is_countable($query->getArrayResult()) ? count($query->getArrayResult()) : 0]);
    }

    /**
     * Creates a new Territory entity.
     *
     * @Route("/new", name="territory_new")
     *
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request): Response
    {
        $territory = new Territory();
        $form = $this->createForm(TerritoryType::class, $territory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($territory);
            $em->flush();

            $request->getSession()
            ->getFlashBag()
            ->add('success', 'Generics.flash.addSuccess');

            if (!$request->request->has('createAndContinue')) {
                return $this->redirectToRoute('territory_edit', ['id' => $territory->getId()]);
            } else {
                $territory = new Territory();
                $form = $this->createForm(TerritoryType::class, $territory);
            }
        }

        return $this->render('admin/territory/new.html.twig', ['territory' => $territory, 'form' => $form->createView()]);
    }

    /**
     * Finds and displays a Territory entity.
     *
     * @Route("/{id}", name="territory_show")
     *
     * @Method("GET")
     *
     * @return Response
     */
    public function showAction(Request $request, Territory $territory)
    {
        return $this->render('admin/territory/show.html.twig', ['territory' => $territory]);
    }

    /**
     * Displays a form to edit an existing territory entity.
     *
     * @Route("/{id}/edit", name="territory_edit")
     *
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Territory $territory): Response
    {
        $editForm = $this->createForm(TerritoryType::class, $territory);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($territory);
            $em->flush();

            $request->getSession()
            ->getFlashBag()
            ->add('success', 'Generics.flash.updateSuccess');

            return $this->redirectToRoute('territory_edit', ['id' => $territory->getId()]);
        }

        return $this->render('admin/territory/edit.html.twig', ['object' => $territory, 'form' => $editForm->createView()]);
    }

    /**
     * Delete a territory entity.
     *
     * @Route("/{id}/delete", name="territory_delete")
     *
     * @Method("GET")
     *
     * @return Response
     */
    public function deleteAction(Request $request, Territory $territory, TerritoryRepository $territoryRepository)
    {
        $em = $this->getDoctrine()->getManager();
        $ids = [$territory->getId()];
        $territoryRepository->batchDelete($ids);
        $request->getSession()
        ->getFlashBag()
        ->add('success', 'Generics.flash.deleteSuccess');

        return $this->redirectToRoute('territory_index');
    }

    /**
     * Creates a new Territory entity.
     *
     * @Route("/new/{id}", name="territory_new_subTerritory")
     *
     * @Method({"GET","POST"})
     *
     * @return Response
     */
    public function newSubTerritoryAction(Request $request, Territory $parent)
    {
        $territory = new Territory();
        $territory->setParent($parent);
        $form = $this->createForm(TerritoryType::class, $territory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($territory);
            $em->flush();

            $request->getSession()
            ->getFlashBag()
            ->add('success', 'Generics.flash.addSuccess');

            if (!$request->request->has('createAndContinue')) {
                return $this->redirectToRoute('territory_edit', ['id' => $territory->getId()]);
            } else {
                $territory = new Territory();
                $form = $this->createForm(TerritoryType::class, $territory);
            }
        }

        return $this->render('admin/territory/new.html.twig', ['territory' => $territory, 'form' => $form->createView()]);
    }

    /**
     * territory batch actions.
     *
     * @Route("/batch", name="territory_batch")
     *
     * @Method({"POST"})
     *
     * @return Response
     */
    public function batchAction(Request $request, NestedTerritoryRepository $territoryRepository)
    {
        if ('batchDelete' === $request->request->get('batch_action')) {
            $ids = $request->request->get('ids');

            if ($ids) {
                $em = $this->getDoctrine()->getManager();
                $territoryRepository->batchDelete($ids, null, $this->getUser());
                $request->getSession()
                ->getFlashBag()
                ->add('success', 'Generics.batch.flash.deleteSuccess');
            } else {
                $request->getSession()
                ->getFlashBag()
                ->add('error', 'Generics.batch.flash.noSelectedItemError');
            }
        }

        return $this->redirectToRoute('admin/territory_index');
    }

    /**
     * territory batch actions.
     *
     * @Route("/{id}/batch", name="territory_batchCities")
     *
     * @Method({"POST"})
     *
     * @return Response
     */
    public function batchCitiesAction(Territory $territory, Request $request)
    {
        if ('batchDeleteFromTerritory' === $request->request->get('batch_action')) {
            $ids = $request->request->get('ids');

            if ($ids) {
                $stmt = $this->getDoctrine()->getEntityManager()
                   ->getConnection()
                    ->prepare('DELETE FROM territories_cities where territory_id='.$territory->getId().' AND city_id IN ('.implode($ids, ',').')');
                $stmt->execute();

                $request->getSession()
                ->getFlashBag()
                ->add('success', 'Generics.batch.flash.deleteSuccess');
            } else {
                $request->getSession()
                ->getFlashBag()
                ->add('error', 'Generics.batch.flash.noSelectedItemError');
            }
        }

        return $this->redirect($this->generateUrl('territory_listCities', ['id' => $territory->getId()]));
    }

    /**
     * add city ton one territory.
     *
     * @Route("/{id}/addCity", name="territory_addCity")
     *
     * @Method({"POST"})
     *
     * @param Territory $territory The Territory entity
     *
     * @return Response
     */
    public function addCityAction(Territory $territory, Request $request, CityRepository $cityRepository)
    {
        $cityId = $request->request->get('cityId');
        $alreadyAssociateCities = $territory->getCities();
        if ($cityId) {
            $em = $this->getDoctrine()->getManager();
            $city = $cityRepository->find($cityId);
            if ($city) {
                if ($alreadyAssociateCities->contains($city)) {
                    $request->getSession()
                    ->getFlashBag()
                    ->add('error', 'Territory.actions.alreadyPresent');
                } else {
                    $territory->addCity($city);
                    $em->persist($territory);
                    $em->flush();
                    $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'Generics.flash.add_city_to_territorySuccess');
                }
            }
        } else {
            $request->getSession()
            ->getFlashBag()
            ->add('error', 'Generics.batch.flash.noSelectedItemError');
        }

        return $this->redirect($this->generateUrl('territory_listCities', ['id' => $territory->getId()]));
    }

    /**
     * List all Cities ssociate to one Territory.
     *
     * @Route("/{id}/listCities", name="territory_listCities")
     *
     * @param Territory $territory The Territory entity
     *
     * @return Response
     */
    public function listCitiesAction(Territory $territory, Request $request, CityRepository $cityRepository)
    {
        $em = $this->getDoctrine()->getManager();

        $session = $request->getSession();

        $territoryId = $territory->getId();
        $name = null;

        $filterData = [];
        $filterForm = $this->createForm(CityFilterType::class);

        // Reset filter
        if ('reset' == $request->get('filter_action')) {
            $session->remove('TerritoryCitiesFilterType');

            return $this->redirect($this->generateUrl('territory_listCities', ['id' => $territoryId]));
        }

        // Filter action
        if ('filter' == $request->get('filter_action')) {
            // Bind values from the request
            $filterForm->handleRequest($request);

            if ($filterForm->isSubmitted()) {
                // Save filter to session
                if ($filterForm->has('territories')) {
                    $filterData['territories'] = $filterForm->get('territories')->getData();
                    $territory = $filterData['territories'];
                }
                if ($filterForm->has('name')) {
                    $filterData['name'] = $filterForm->get('name')->getData();
                    $name = $filterData['name'];
                }
                if ($filterForm->has('zipCode')) {
                    $filterData['zipCode'] = $filterForm->get('zipCode')->getData();
                }
                if ($filterForm->has('department')) {
                    $filterData['department'] = $filterForm->get('department')->getData();
                }
                $session->set('TerritoryCitiesFilterType', $filterData);
            }
        } else {
            // Get filter from session

            if ($session->has('TerritoryCitiesFilterType')) {
                $filterData = $session->get('TerritoryCitiesFilterType');
                if (array_key_exists('territories', $filterData)) {
                    $territory = $filterData['territories'];
                }
                if ($territory) {
                    $territory = $this->getDoctrine()->getEntityManager()->merge($territory);
                    $filterData['territories'] = $territory;
                }
                $filterForm = $this->createForm(CityFilterType::class, $filterData, ['data_class' => null]);
                $territory = $em->merge($filterData['territories']);
                $name = $filterData['name'];
            } else {
                $filterData = $session->get('TerritoryCitiesFilterType');
                $filterData['territories'] = $territory;
                $filterData['name'] = $name;
                $filterForm = $this->createForm(CityFilterType::class, $filterData, ['data_class' => null]);
            }
        }

        $cities = $cityRepository->listTerritoryCities($territory->getId(), $filterData);

        return $this->render('admin/territory/listCities.html.twig', ['cities' => $cities, 'territory' => $territory, 'search_form' => $filterForm->createView(), 'subFolder' => false]);
    }

    /**
     * List all Cities associate to one Territory en sub territories.
     *
     * @Route("/{id}/listSubTerritoryCities", name="territory_listSubTerritoryCities")
     *
     * @param Territory $territory The Territory entity
     *
     * @return Response
     */
    public function listSubTerritoryCitiesAction(Territory $territory, Request $request, CityRepository $cityRepository)
    {
        $em = $this->getDoctrine()->getManager();

        $session = $request->getSession();

        $territoryId = $territory->getId();
        $name = null;

        $filterData = [];
        $filterForm = $this->createForm(CityFilterType::class);

        // Reset filter
        if ('reset' == $request->get('filter_action')) {
            $session->remove('SubTerritoryCitiesFilterType');

            return $this->redirect($this->generateUrl('territory_listSubTerritoryCities', ['id' => $territoryId]));
        }

        // Filter action
        if ('filter' == $request->get('filter_action')) {
            // Bind values from the request
            $filterForm->handleRequest($request);

            if ($filterForm->isValid()) {
                // Save filter to session
                if ($filterForm->has('territories')) {
                    $filterData['territories'] = $filterForm->get('territories')->getData();
                    $territory = $filterData['territories'];
                }
                if ($filterForm->has('name')) {
                    $filterData['name'] = $filterForm->get('name')->getData();
                    $name = $filterData['name'];
                }
                if ($filterForm->has('zipCode')) {
                    $filterData['zipCode'] = $filterForm->get('zipCode')->getData();
                }
                if ($filterForm->has('department')) {
                    $filterData['department'] = $filterForm->get('department')->getData();
                }
                $session->set('SubTerritoryCitiesFilterType', $filterData);
            }
        } else {
            // Get filter from session

            if ($session->has('SubTerritoryCitiesFilterType')) {
                $filterData = $session->get('SubTerritoryCitiesFilterType');
                if (array_key_exists('territories', $filterData)) {
                    $territory = $filterData['territories'];
                }
                if ($territory) {
                    $territory = $this->getDoctrine()->getEntityManager()->merge($territory);
                    $filterData['territories'] = $territory;
                }
                $filterForm = $this->createForm(CityFilterType::class, $filterData, ['data_class' => null]);
                $territory = $em->merge($filterData['territories']);
                $name = $filterData['name'];
            } else {
                $filterData = $session->get('SubTerritoryCitiesFilterType');
                $filterData['territories'] = $territory;
                $filterData['name'] = $name;
                $filterForm = $this->createForm(CityFilterType::class, $filterData, ['data_class' => null]);
            }
        }
        $territoryRepository = $em->getRepository(Territory::class);
        $childrens = $territoryRepository->getChildren($node = $territory, $direct = false, $sortByField = null, $direction = 'asc', $includeNode = true);
        $cities = $cityRepository->listSubTerritoryCities($childrens, $filterData);

        return $this->render('admin/territory/listCities.html.twig', ['cities' => $cities, 'territory' => $territory, 'search_form' => $filterForm->createView(), 'subFolder' => true]);
    }

    /**
     * Delete Association between one territory and one city.
     *
     * @Route("/{id}/deleteCity/{cityId}", name="territory_deleteCity")
     *
     * @param Territory $territory The Territory entity
     * @param int       $cityId    id of The City entity
     *
     * @return Response
     */
    public function deleteCityToTerritoryAction(Request $request, Territory $territory, $cityId, CityRepository $cityRepository)
    {
        $em = $this->getDoctrine()->getManager();
        $city = $cityRepository->find($cityId);

        $territory->removeCity($city);
        $city->removeTerritory($territory);
        $em->persist($city);
        $em->persist($territory);
        $em->flush();

        $request->getSession()
        ->getFlashBag()
        ->add('success', 'Generics.flash.deleteSuccess');

        return $this->redirect($this->generateUrl('territory_listCities', ['id' => $territory->getId()]));
    }

    /**
     * Delete All Association between one city ans territories.
     *
     * @Route("/{id}/deleteCity/{cityId}/massive", name="territory_SubdeleteCity")
     *
     * @param Territory $territory The Territory entity
     * @param int       $cityId    id of The City entity
     *
     * @return Response
     */
    public function deleteCityToMassiveTerritoryAction(Request $request, Territory $territory, $cityId, CityRepository $cityRepository)
    {
        $em = $this->getDoctrine()->getManager();
        $city = $cityRepository->find($cityId);

        $territories = $city->getTerritories();
        foreach ($territories as $territory) {
            $territory->removeCity($city);
            $city->removeTerritory($territory);
            $em->persist($city);
            $em->persist($territory);
        }
        $em->flush();

        $request->getSession()
        ->getFlashBag()
        ->add('success', 'Generics.flash.deleteMassiveSuccess');

        return $this->redirect($this->generateUrl('territory_listSubTerritoryCities', ['id' => $territory->getId()]));
    }

    /**
     * Creates a form to delete a Territory entity.
     *
     * @param Territory $territory The Territory entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Territory $territory)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('territory_delete', ['id' => $territory->getId()]))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
