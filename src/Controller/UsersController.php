<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use App\Form\UserFormType;
use App\Form\UserProfileFormType;
use App\Form\DeleteFormType;

use App\Entity\Users;

use App\Repository\UsersRepository;

class UsersController extends AbstractController
{
    private $em;
    private $router;
    private $translator;

    public function __construct(EntityManagerInterface $em, UrlGeneratorInterface $router, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->router = $router;
        $this->translator = $translator;
    }

    #[Route('/users', name: 'app_users')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$this->getUser() || !$this->isGranted('IS_AUTHENTICATED')) {
            return $this->redirectToRoute('app_login');
        }

        $pages = array("page" => 1,"next" => false,"prev" => false);

        if(isset($_GET["page"]) && $_GET["page"] > 0) {
            $pages["page"] = intval($_GET["page"]);
        } else {
            $pages["page"] = 1;
        }

        if(isset($_GET["perpage"]) && $_GET["perpage"] > 0) {
            $pages["perpage"] = intval($_GET["perpage"]);
        } else {
            $pages["perpage"] = 10;
        }

        $repository = $this->em->getRepository(Users::class);
        $users = $repository->findBy(array(), array('id' => 'DESC'), $pages["perpage"], ($pages["page"] - 1) * $pages["perpage"]);
        $pages["totalitems"] = $repository->getTotalRows();
        $pages["start"] = $pages["totalitems"] - (($pages["page"] - 1) * $pages["perpage"]); 
        $pages["end"] = $pages["totalitems"] - (($pages["page"] - 1) * $pages["perpage"]) - $pages['perpage'] + 1;
        if ($pages["end"] < 0) {
            $pages["end"] = 1;
        }

        if(count($users) == 0 && $pages["totalitems"] != 0) {
            return $this->redirectToRoute('app_logs');
        }

        $pages["total"] = ceil($pages["totalitems"] / $pages['perpage']);
        if($pages["totalitems"] / $pages["perpage"] > $pages["page"]) {
            $pages["next"] = true;
        }
        if($pages["page"] - 1 > 0) {
            $pages["prev"] = true;
        }

        return $this->render('users/index.html.twig', [
            'users' => $users,
            'pages' => $pages,
            'page' => array(
                'menu' => array(
                    'category' => 'settings',
                    'item' => 'users'
                ),
                'pretitle' => $this->translator->trans("Settings"),
                'title' => $this->translator->trans("Users"),
                'actions' => array(
                    0 => array(
                        'primary' => true,
                        'name' => $this->translator->trans("Add"),
                        'target' => $this->router->generate('app_users_add'),
                        'icon' => "plus"
                    ),
                ),
            ),
            'breadcrumbs' => array(array('name' => $this->translator->trans("Users"), 'url' => $this->router->generate('app_users'))),
        ]);
    }

    #[Route('/users/add', name: 'app_users_add')]
    public function add(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$this->getUser() || !$this->isGranted('IS_AUTHENTICATED')) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(UserFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formdata = $form->getData();

            $password1 = $form->get("password1")->getData();
            $password2 = $form->get("password2")->getData();

            if($password1 == $password2 && $password1 != "") {
                $formdata->setPassword(
                    $userPasswordHasher->hashPassword(
                        $formdata,
                        $form->get('password1')->getData()
                    )
                );
            } else {
                $this->addFlash('danger', $this->translator->trans("Passwords do not match"));
                return $this->redirectToRoute('app_users_add');
            }

            $is_admin = $form->get("isAdmin")->getData();

            $domain_roles = $form->get("roles")->getData();
            $roles = array();
            foreach($domain_roles as $domain) {
                $formdata->addDomain($domain);
            }
            if($is_admin == true) {
                array_push($roles, "ROLE_ADMIN");
            }
            $formdata->setRoles($roles);

            $this->em->persist($formdata);
            $this->em->flush();

            return $this->redirectToRoute('app_users');
        }
        $setup['users_form'] = $form->createView();

        return $this->render('users/edit.html.twig', [
            'page' => array(
                'menu' => array(
                    'category' => 'settings',
                    'item' => 'users'
                ),
                'pretitle' => $this->translator->trans("Settings"),
                'title' => $this->translator->trans("Add user"),
                'actions' => array(),
            ),
            'user' => null,
            'form' => $form,
            'breadcrumbs' => array(
                array('name' => $this->translator->trans("Users"), 'url' => $this->router->generate('app_users')),
                array('name' => $this->translator->trans("Add new user"), 'url' => $this->router->generate('app_users'))
            ),
        ]);
    }

    #[Route('/users/edit/{user}', name: 'app_users_edit')]
    public function edit(Users $user, Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        if (!$this->getUser() || !$this->isGranted('IS_AUTHENTICATED')) {
            return $this->redirectToRoute('app_login');
        }

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(UserFormType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formdata = $form->getData();

            $password1 = $form->get("password1")->getData();
            $password2 = $form->get("password2")->getData();

            if(!is_null($password1)) {
                if($password1 == $password2) {
                    $formdata->setPassword(
                        $userPasswordHasher->hashPassword(
                            $formdata,
                            $form->get('password1')->getData()
                        )
                    );
                    $this->addFlash('success', $this->translator->trans("Password updated"));
                } else {
                    $this->addFlash('danger', $this->translator->trans("Passwords do not match"));
                    return $this->redirectToRoute('app_users_edit', ['user' => $user->getId()]);
                }
            }

            $is_admin = $form->get("isAdmin")->getData();

            $domain_roles = $form->get("roles")->getData();
            $roles = array();
            foreach($domain_roles as $domain) {
                $formdata->addDomain($domain);
            }
            if($is_admin == true) {
                array_push($roles, "ROLE_ADMIN");
            }
            $formdata->setRoles($roles);

            $this->em->persist($formdata);
            $this->em->flush();

            return $this->redirectToRoute('app_users');
        }

        return $this->render('users/edit.html.twig', [
            'page' => array(
                'menu' => array(
                    'category' => 'settings',
                    'item' => 'users'
                ),
                'pretitle' => $this->translator->trans("Settings"),
                'title' => $this->translator->trans("Edit user ".$user->getEmail()),
                'actions' => array(),
            ),
            'user' => $user,
            'form' => $form,
            'breadcrumbs' => array(
                array('name' => $this->translator->trans("Users"), 'url' => $this->router->generate('app_users')),
                array('name' => $user->getEmail(), 'url' => $this->router->generate('app_users'))
            ),
        ]);
    }

    #[Route('/user/profile', name: 'app_user_profile')]
    public function edit_profile(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        if (!$this->getUser() || !$this->isGranted('IS_AUTHENTICATED')) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(UserProfileFormType::class, $this->getUser());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formdata = $form->getData();
            
            if(!empty($form->get("password0")->getData())) {
                $formdata->setPassword($this->getUser()->getPassword());

                if($userPasswordHasher->isPasswordValid($this->getUser(), $form->get("password0")->getData())) {
                    if($form->get("password1")->getData() == $form->get("password2")->getData()){
                        $formdata->setPassword(
                            $userPasswordHasher->hashPassword(
                                $formdata,
                                $form->get('password1')->getData()
                            )
                        );
                        $this->addFlash('success', $this->translator->trans("Password updated"));
                    } else {
                        $this->addFlash('danger', $this->translator->trans("Passwords do not match"));
                    }
                } else {
                    $this->addFlash('danger', $this->translator->trans("Current password is incorrect"));
                }
            }

            $this->em->persist($formdata);
            $this->em->flush();

            return $this->redirectToRoute('app_user_profile');
        }
        
        return $this->render('users/edit_profile.html.twig', [
            'page' => array(
                'menu' => array(
                    'category' => 'user',
                    'item' => 'profile'
                ),
                'pretitle' => $this->translator->trans("User"),
                'title' => $this->translator->trans("Profile"),
                'actions' => array(),
            ),
            'user' => $this->getUser(),
            'form' => $form,
            'breadcrumbs' => array(
                array('name' => $this->translator->trans("User"), 'url' => "#"),
                array('name' => $this->translator->trans("Profile"), 'url' => $this->router->generate('app_user_profile'))
            ),
        ]);
    }

    #[Route('/users/delete/{user}', name: 'app_users_delete')]
    public function delete(Users $user, Request $request): Response
    {
        if (!$this->getUser() || !$this->isGranted('IS_AUTHENTICATED')) {
            return $this->redirectToRoute('app_login');
        }

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(DeleteFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $formdata = $form->getData();
            if($formdata['item'] == $user->getEmail()) {
                $this->addFlash('success', $this->translator->trans('User deleted'));

                $this->em->remove($user);
                $this->em->flush();
                
                return $this->redirectToRoute('app_users');
            } else {
                $this->addFlash('danger', $this->translator->trans('The name you entered does not match the users email'));
            }
        }

        return $this->render('base/delete.html.twig', [
            'page' => array(
                'menu' => array(
                    'category' => 'users',
                    'item' => 'edit'
                ),
                'pretitle' => $this->translator->trans("Users"),
                'title' => $this->translator->trans("Delete user")." ".$user->getEmail(),
                'actions' => array(),
            ),
            'item' => $user->getEmail(),
            'form' => $form,
            'breadcrumbs' => array(
                array('name' => $this->translator->trans("Users"), 'url' => $this->router->generate('app_users')),
                array('name' => $user->getEmail(), 'url' => $this->router->generate('app_users')),
                array('name' => $user->getEmail(), 'url' => $this->router->generate('app_users_delete', ['user' => $user->getId()]))
            ),
        ]);

        $this->em->remove($user);
        $this->em->flush();

        return $this->redirectToRoute('app_users');
    }
}
