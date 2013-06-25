<?php
namespace OliverHader\Mapping\Assignment;
use OliverHader\Mapping\Domain\Object\ProcessorTask;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Oliver Hader <oliver.hader@typo3.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * @author Oliver Hader <oliver.hader@typo3.org>
 */
interface DataProviderInterface {

	/**
	 * @param array $configuration
	 * @return void
	 */
	public function setConfiguration(array $configuration);

	/**
	 * @param string $tableName
	 * @param array $record
	 * @return boolean
	 */
	public function canAssign($tableName, array $record = NULL);

	/**
	 * @param string $tableName
	 * @param array $record
	 * @return boolean
	 */
	public function canRender($tableName, array $record = NULL);

	/**
	 * @param string $tableName
	 * @param array $record
	 * @return array
	 */
	public function getNodes($tableName, array $record);

	/**
	 * @param string $tableName
	 * @param array $record
	 * @return array
	 */
	public function getAssignment($tableName, array $record);

	/**
	 * @param ProcessorTask $processorTask
	 * @return array
	 */
	public function getContentReplacement(ProcessorTask $processorTask);

}
?>