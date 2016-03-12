<?php

namespace Paraunit\Parser;

use Paraunit\Printer\OutputContainer;

/**
 * Class FailureParser
 * @package Paraunit\Parser
 */
class FailureParser extends AbstractParser implements JSONParserChainElementInterface
{
    const TAG = 'failure';
    const TITLE = 'failures';
    const PARSING_REGEX = '/(?:There (?:was|were) \d+ failures?:\n\n)((?:.|\n)+)(?=\nFAILURES)/';

    /**
     * AbstractParser constructor.
     *
     * @param OutputContainer $outputContainer
     */
    public function __construct(OutputContainer $outputContainer)
    {
        parent::__construct($outputContainer, 'F', 'fail');
    }
}
