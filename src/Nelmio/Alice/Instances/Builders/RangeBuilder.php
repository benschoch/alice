<?php

/*
 * This file is part of the Alice package.
 *
 * (c) Nelmio <hello@nelm.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nelmio\Alice\Instances\Builders;

use Nelmio\Alice\Instances\Instance;
use Nelmio\Alice\Util\FlagParser;

class RangeBuilder extends BaseBuilder {

	private $matches = array();

	/**
	 * {@inheritDoc}
	 */
	public function canBuild($name)
	{
		return preg_match('#\{([0-9]+)\.\.(\.?)([0-9]+)\}#i', $name, $this->matches);
	}

	/**
	 * {@inheritDoc}
	 */
	public function build($class, $name, array $spec)
	{
		$instances = array();

		$from = $this->matches[1];
		$to = empty($this->matches[2]) ? $this->matches[3] : $this->matches[3] - 1;
		if ($from > $to) {
			list($to, $from) = array($from, $to);
		}
		for ($currentIndex = $from; $currentIndex <= $to; $currentIndex++) {
			$currentName = str_replace($this->matches[0], $currentIndex, $name);
			$this->processor->setCurrentValue($currentIndex);
			$instance = new Instance($class, $currentName, $spec, $this->processor, $this->typeHintChecker, $currentIndex);
			$this->processor->unsetCurrentValue();
			$instances[] = $instance;
		}

		return $instances;
	}

}