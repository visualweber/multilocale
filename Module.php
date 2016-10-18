<?php

/**
 * Copyright (c) 2016 Visual Weber.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of the
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author      Visual Weber <contact@visualweber.com>
 * @copyright   2016 Visual Weber.
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://visualweber.com
 */

namespace MultiLocale;

use Locale,
    Zend\ModuleManager\Feature,
    Zend\EventManager\EventInterface,
    Zend\Mvc\MvcEvent,
    Zend\Stdlib\ResponseInterface,
    Zend\Validator\AbstractValidator;

class Module implements Feature\AutoloaderProviderInterface, Feature\ConfigProviderInterface, Feature\BootstrapListenerInterface {

    // Private storage of all our local languages
    private $locales = [
        'vi' => 'vi_VN', // tiếng việt
        'en' => 'en_GB', // tiếng anh
        'en-US' => 'en_GB' // tiếng anh
    ];

    public function getAutoloaderConfig() {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(EventInterface $e) {
        $app = $e->getApplication();
        $sm = $app->getServiceManager();
        $language = 'vi_VN';

        $detector = $sm->get('MultiLocale\Locale\Detector');
        $locale = $detector->detect($app->getRequest(), $app->getResponse());

        if ($locale instanceof ResponseInterface):
            /**
             * When the detector returns a response, a strategy has updated the response
             * to reflect the found locale.
             *
             * To redirect the user to this new URI, we short-circuit the route event. There
             * is no option to short-circuit the bootstrap event, so we attach a listener to
             * the route and let the application finish the bootstrap first.
             *
             * The listener is attached at PHP_INT_MAX to return the response as early as
             * possible.
             */
            $em = $app->getEventManager();
            $em->attach(MvcEvent::EVENT_ROUTE, function($e) use ($locale) {
                return $locale;
            }, PHP_INT_MAX);
        else:
            if (isset($this->locales[$locale]) && $this->locales[$locale]):
                $locale = $this->locales[$locale];
            endif;
            // ZF2 only supports the underscore, like en_GB
            $language = str_replace('-', '_', $locale);
        endif;

        $translator = $sm->get('translator'); // im using service alias 'translator' instead of 'MvcTranslator'
        $translator
                ->setLocale($language)
                ->setFallbackLocale('vi_VN'); // Make sure that our fallback has been set in case we could not find a locale
        AbstractValidator::setDefaultTranslator($translator);

        Locale::setDefault($locale);
    }

}
