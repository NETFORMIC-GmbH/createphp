<?php
/**
 * @copyright CONTENT CONTROL GmbH, http://www.contentcontrol-berlin.de
 * @author David Buchmann <david@liip.ch>
 * @license Dual licensed under the MIT (MIT-LICENSE.txt) and LGPL (LGPL-LICENSE.txt) licenses.
 * @package Midgard.CreatePHP
 */

namespace Midgard\CreatePHP\Extension\Twig;

use Midgard\CreatePHP\Metadata\RdfTypeFactory;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * A twig token parser for the createphp tag extension.
 *
 * @package Midgard.CreatePHP
 */
class CreatephpTokenParser extends AbstractTokenParser
{
    private $factory;

    /**
     * Constructor.
     *
     * Attributes can be added to the tag by passing names as the options
     * array. These values, if found, will be passed to the factory and node.
     *
     * @param RdfTypeFactory $factory    The asset factory
     */
    public function __construct(RdfTypeFactory $factory)
    {
        $this->factory = $factory;
    }

    public function parse(Token $token)
    {
        $stream = $this->parser->getStream();

        // the object might be an expression like container.field
        $object = $this->parser->getExpressionParser()->parseExpression();

        $var = null;
        if ($stream->test(Token::NAME_TYPE, 'as')) {
            $stream->next();
            if ($stream->test(Token::OPERATOR_TYPE, '=')) {
                $stream->expect(Token::OPERATOR_TYPE, '=');
            }
            $var = $stream->expect(Token::STRING_TYPE)->getValue();
        }

        $noautotag = false;
        if ($stream->test(Token::NAME_TYPE, 'noautotag')) {
            $noautotag = true;
            $stream->next();
        }

        $stream->expect(Token::BLOCK_END_TYPE);

        $endtag = 'end'.$this->getTag();
        $test = function(Token $token) use($endtag) { return $token->test($endtag); };
        $body = $this->parser->subparse($test, true);
        $stream->expect(Token::BLOCK_END_TYPE);

        return new CreatephpNode($body, $object, $var, !$noautotag, $token->getLine(), $this->getTag());
    }

    public function getTag()
    {
        return 'createphp';
    }
}
