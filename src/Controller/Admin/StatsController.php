<?php

namespace App\Controller\Admin;

use App\FormFilter\AbrisFilterType;
use App\Repository\AbrisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/stats")
 */
class StatsController extends Controller
{
    /**
     * @Route("/", name="stats_index", methods="GET|POST")
     */
    public function indexAction(AbrisRepository $abrisRepository, Request $request): Response
    {
        $session = $request->getSession();
        $filterData = [];
        $filterForm = $this->createForm(AbrisFilterType::class);

        if ('reset' == $request->get('filter_action')) { // Reset filter
            $session->remove('statsFilter');

            return $this->redirect($this->generateUrl('stats_index'));
        }
        // dd($session->get('statsFilter'));

        if ('filter' == $request->get('filter_action')) { // Filter action
            $filterForm->handleRequest($request); // Bind values from the request
            if ($filterForm->isSubmitted() && $filterForm->isValid()) {
                if ($filterForm->has('name')) {
                    $filterData['name'] = $filterForm->get('name')->getData();
                }
                if ($filterForm->has('type')) {
                    $filterData['type'] = $filterForm->get('type')->getData();
                }

                $session->set('statsFilter', $filterData); // Save filter to session
            }
        } elseif ($session->has('statsFilter')) {
            $filterData = $session->get('statsFilter');
            if (array_key_exists('name', $filterData)) {
                $filterForm->get('name')->setData($filterData['name']);
            }
            if (array_key_exists('type', $filterData) && '' != $filterData['type']) {
                $type = $filterData['type'];
                $type = $this->getDoctrine()->getManager()->merge($type);
                $filterForm->get('type')->setData($type);
            }
        }

        // envoi du user logué si son rôle est autre que admin
        if (!$this->getUser()->hasRole('ROLE_ADMIN')) {
            $filterData['userID'] = $this->getUser()->getId();
        }

        $abrisResults = $abrisRepository->search($filterData)->getResult();

        return $this->render('admin/stats/index.html.twig', ['abrisResults' => $abrisResults, 'search_form' => $filterForm->createView()]);
    }
}
