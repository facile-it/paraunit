<?php

namespace Paraunit\Parser;

use Paraunit\Printer\OutputContainer;

/**
 * Class ErrorParser
 * @package Paraunit\Parser
 */
class ErrorParser extends AbstractParser implements JSONParserChainElementInterface
{
    const TAG = 'error';
    const TITLE = 'Errors';
    const PARSING_REGEX = '/(?:There (?:was|were) \d+ errors?:\n\n)((?:.|\n)+)(?:\n--|FAILURES)/U';

    /**
     * AbstractParser constructor.
     *
     * @param OutputContainer $outputContainer
     */
    public function __construct(OutputContainer $outputContainer)
    {
        parent::__construct($outputContainer, 'E', 'error');
    }
}
