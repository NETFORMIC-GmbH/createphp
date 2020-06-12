<?php
/**
 * @copyright CONTENT CONTROL GmbH, http://www.contentcontrol-berlin.de
 * @author David Buchmann <david@liip.ch>
 * @license Dual licensed under the MIT (MIT-LICENSE.txt) and LGPL (LGPL-LICENSE.txt) licenses.
 * @package Midgard.CreatePHP
 */

namespace Midgard\CreatePHP\Extension\Twig;

use Twig\Environment;
use Twig\Error\RuntimeError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

use Midgard\CreatePHP\NodeInterface;
use Midgard\CreatePHP\Type\TypeInterface;
use Midgard\CreatePHP\Metadata\RdfTypeFactory;

/**
 * Twig Extension to integrate createphp into Twig.
 *
 * Provides a createphp construct and the functions createphp_attributes and createphp_content
 *
 * @package Midgard.CreatePHP
 */
class CreatephpExtension extends AbstractExtension
{
    protected $typeFactory;
    protected $environment;

    public function __construct(RdfTypeFactory $typeFactory)
    {
        $this->typeFactory = $typeFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function initRuntime(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * Returns the token parser instance to add to the existing list.
     *
     * @return array An array of Twig_TokenParser instances
     */
    public function getTokenParsers()
    {
        return [
            // {% createphp model %}
            new CreatephpTokenParser($this->typeFactory)
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('createphp_attributes', [$this, 'renderAttributes'], array('is_safe' => array('html'))),
            new TwigFunction('createphp_content', [$this, 'renderContent'], array('is_safe' => array('html')))
        ];
    }

    /**
     * Renders the attributes of the passed node
     *
     * Example usage in Twig templates:
     *
     *     <span {{ createphp_attributes(entity) }}>
     *
     * Example usage with optional $removeAttr
     *
     *      {% set removeAttr = ['partof', 'rev', 'ect...'] %}
     *      <span {{ createphp_attributes(entity, removeAttr) }}>
     *
     *
     * @param NodeInterface $node The node (entity, property or collection) for which to render the attributes
     *
     * @return string The html markup
     */
    public function renderAttributes(NodeInterface $node, $attributesToSkip = [])
    {
        return $node->renderAttributes($attributesToSkip);
    }

    /**
     * Renders the content of the passed node.
     *
     * Example usage:
     *
     *      <div {{ createphp_attributes(entity) }}>
     *          <span {{ createphp_attributes(entity.title) }}>
     *              {{ createphp_content(entity.title}}
     *          </span>
     *      </div>
     *
     * @param NodeInterface $node the node for which to render the content
     *
     * @return string The html markup
     */
    public function renderContent(NodeInterface $node)
    {
        return $node->renderContent();
    }

    public function createEntity($model)
    {
        if (!is_object($model)) {
            throw new RuntimeError('The model to create the entity from must be a class');
        }

        $type = $this->typeFactory->getTypeByObject($model);
        if (!($type instanceof TypeInterface)) {
            throw new RuntimeError('Could not find metadata for ' . get_class($model));
        }

        return $type->createWithObject($model);
    }

}
