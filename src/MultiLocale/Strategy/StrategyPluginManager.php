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

namespace MultiLocale\Strategy;

use Zend\ServiceManager\AbstractPluginManager;

class StrategyPluginManager extends AbstractPluginManager
{
    /**
     * {@inheritDocs}
     */
    protected $invokableClasses = array(
        'cookie'         => 'MultiLocale\Strategy\CookieStrategy',
        'host'           => 'MultiLocale\Strategy\HostStrategy',
        'acceptlanguage' => 'MultiLocale\Strategy\HttpAcceptLanguageStrategy',
        'query'          => 'MultiLocale\Strategy\QueryStrategy',
        'uripath'        => 'MultiLocale\Strategy\UriPathStrategy',
    );

    /**
     * Validate the plugin
     *
     * Checks that the helper loaded is an instance of StrategyInterface.
     *
     * @param  mixed                            $plugin
     * @return void
     * @throws Exception\InvalidStrategyException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof StrategyInterface) {
            // we're okay
            return;
        }

        throw new Exception\InvalidStrategyException(sprintf(
            'Plugin of type %s is invalid; must implement %s\StrategyInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}