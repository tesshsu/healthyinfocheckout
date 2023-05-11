<?php

namespace HealthyInfoCheckOut\Controller;
use Symfony\Component\HttpFoundation\Response;

class HealthyQuestionController extends FrameworkBundleAdminController
{

    public function demoAction()
    {
        return new Response('Hello world!');
    }

    // add other actions here
}
