<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;
use App\Repository\CityRepository;
use App\Repository\TerritoryRepository;
use App\Repository\ListingValueRepository;
use App\Repository\HelpMessageRepository;
use App\Repository\AbrisRepository;
use App\Repository\PageRepository;

/**
 * Description of AjaxController.
 *
 * @Route("/ajax")
 */
class AjaxController extends Controller
{
    /**
     * autocomplete abris action. search in abris.
     *
     * @Route("/autocomplete-abris", name="autocomplete_abris", options={"expose"=true})
     */
    public function autoCompleteAbrisAction(AbrisRepository $abrisRepository, Request $request)
    {
        $q = $request->query->get('q');
        $return = [];
        $items = [];
        $entities = $abrisRepository->autoComplete($q, $request->query->get('page_limit'), $request->query->get('page'));
        foreach ($entities as $entity) {
            $label = ''.$entity['name'];

            $items[] = ['id' => $entity['id'], 'text' => $label, 'label' => $label];
        }

        $return['results'] = $items;
        $return['more'] = true;

        $return['length'] = is_countable($entities) ? count($entities) : 0;
        $return['items'] = $items;

        return new Response(json_encode($return, JSON_THROW_ON_ERROR), $return ? 200 : 404);
    }

    /**
     * autocomplete city action. search in city.
     *
     * @Route("/autocomplete-city", name="autocomplete_city", options={"expose"=true})
     */
    public function autoCompleteCityAction(CityRepository $cityRepository, Request $request)
    {
        $q = $request->query->get('q');
        $return = [];
        $items = [];
        $entities = $cityRepository->autoComplete($q, $request->query->get('page_limit'), $request->query->get('page'));
        foreach ($entities as $entity) {
            $label = ''.$entity['name'];
            if ($entity['zipCode']) {
                $label .= ' ('.$entity['zipCode'].')';
            }
            $items[] = ['id' => $entity['id'], 'text' => $label, 'label' => $label];
        }

        $return['results'] = $items;
        $return['more'] = true;

        $return['length'] = is_countable($entities) ? count($entities) : 0;
        $return['items'] = $items;

        return new Response(json_encode($return, JSON_THROW_ON_ERROR), $return ? 200 : 404);
    }

    /**
     * Set the order(weight) of an item of ListingValue.
     *
     * @Route("/set-order-listing-value", name="set_order_listing_value", options={"expose"=true})
     */
    public function setOrderItemListingValue(ListingValueRepository $listingValueRepository, Request $request)
    {
        $q = $request->request->get('id_value');
        $w = $request->request->get('orderValue');
        if (!empty($q) && !empty($w)) {
            $return = [];
            $em = $this->getDoctrine()->getManager();

            $listingValue = $listingValueRepository->find($q);
            $listingValue->setWeight($w);
            $em->flush($listingValue);
            $return['success'] = true;

            return new Response(json_encode($return), \Symfony\Component\HttpFoundation\Response::HTTP_OK);
        } else {
            return new Response('', \Symfony\Component\HttpFoundation\Response::HTTP_OK);
        }
    }

    /**
     * Get children sub type for diagnosticState type.
     *
     * @Route("/get-help-message-for-listing-value", name="get_help_message_for_listing_value", methods="GET|POST", options={"expose"=true})
     */
    public function getHelpMessageForListingValue(HelpMessageRepository $helpMessageRepository, Request $request)
    {
        if ($request->query->has('id')) {
            $q = $request->query->get('id');
        }
        if ($request->request->has('id')) {
            $q = $request->request->get('id');
        }

        if (!empty($q)) {
            if ($id = $helpMessageRepository->find($q)) {
                $response = $this->forward('App\Controller\Admin\HelpMessageController::blocShowAction', ['id' => $id]);

                return $response;
            } else {
                return new Response('', \Symfony\Component\HttpFoundation\Response::HTTP_OK);
            }
        } else {
            return new Response('', \Symfony\Component\HttpFoundation\Response::HTTP_OK);
        }
    }



    /**
     * @Route("/autocomplete-search-help-message", name="autocomplete_search_help_message", options={"expose"=true})
     */
    public function autoCompleteSearchHelpMessageAction(HelpMessageRepository $helpMessageRepository, Request $request)
    {
        $q = $request->query->get('q');
        $return = [];
        $items = [];

        $child_entities = $helpMessageRepository->autoComplete($q, $request->query->get('page_limit'), $request->query->get('page'));

        foreach ($child_entities as $entity) {
            $items[] = ['id' => $entity->getId(), 'text' => $entity->getName()];
        }
        $return['results'] = $items;

        return new Response(json_encode($return, JSON_THROW_ON_ERROR), $return ? 200 : 404);
    }

    /**
     * autocomplete territory action. search in territories.
     *
     * @Route("/autocomplete_territory", name="autocomplete_territory", options={"expose"=true})
     */
    public function autoCompleteTerritoryAction(TerritoryRepository $territoryRepository, Request $request)
    {
        $q = $request->query->get('q');
        $return = [];
        $items = [];
        $entities = $territoryRepository->autoComplete($q, $request->query->get('page_limit'), $request->query->get('page'));
        foreach ($entities as $entity) {
            $label = ''.$entity['name'];
            $items[] = ['id' => $entity['id'], 'text' => $label, 'label' => $label];
        }

        $return['results'] = $items;
        $return['more'] = true;

        $return['length'] = is_countable($entities) ? count($entities) : 0;
        $return['items'] = $items;

        return new Response(json_encode($return, JSON_THROW_ON_ERROR), $return ? 200 : 404);
    }


    /**
     * @Route("/page/{id}", name="ajax_page", options={"expose"=true})
     */
    public function getPageContent($id, PageRepository $pageRepository)
    {
        $page = $pageRepository->find($id);
        $return = '';
        if($page) {
            $return = '<div class="modal-header">
        <h5 class="modal-title">'.$page->getName().'</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body"><p>'.$page->getBody().'</p></div>';
        }

        return new Response($return, $return ? 200 : 404);

    }


}
