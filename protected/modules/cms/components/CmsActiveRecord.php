<?php
/**
 * CmsActiveRecord class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2011, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.components
 */

/**
 * Active record class that provides various base functionality.
 * All cms active records should be extended from this class.
 */
class CmsActiveRecord extends CActiveRecord
{
	/**
	 * @return array the default scope.
	 */
	public function defaultScope()
	{
		$scope = parent::defaultScope();

		if ($this->hasAttribute('deleted'))
		{
			$prefix = $this->getTableAlias(true, false);
			$condition = $prefix . '.deleted=0';

			if (isset($scope['condition']))
				$scope['condition'] .= ' AND ' . $condition;
			else
				$scope['condition'] = $condition;
		}

		return $scope;
	}

	/**
	 * Actions to be taken before saving the record.
	 * @return boolean whether the record can be saved
	 */
	public function beforeSave()
	{
		if (parent::beforeSave())
		{
			$now = new CDbExpression('NOW()');
			$userId = Yii::app()->user->id;

			if ($this->isNewRecord)
			{
				// We are creating a new record.
				if ($this->hasAttribute('created'))
					$this->created = $now;

				if ($this->hasAttribute('creatorId') && $userId !== null)
					$this->creatorId = $userId;
			}
			else
			{
				// We are updating an existing record.
				if ($this->hasAttribute('modified'))
					$this->modified = $now;

				if ($this->hasAttribute('modifierId') && $userId !== null)
					$this->modifierId = $userId;
			}

			return true;
		}
		else
			return false;
	}

	/**
	 * Actions to be taken before calling delete.
	 * @param boolean $soft indicates whether to perform a "soft" delete
	 * @return boolean whether the record can be deleted
	 */
	public function beforeDelete($soft)
	{
		if (parent::beforeDelete() && $soft && $this->hasAttribute('deleted'))
		{
			$this->deleted = '1';
			$this->save(false);
			return false;
		}
		else
			return true;
	}

	/**
	 * Deletes the row corresponding to this active record.
	 * @param boolean $soft indicates whether to perform a "soft" delete
	 * @return boolean whether the deletion is successful
	 * @throws CDbException if the record is new
	 */
	public function delete($soft = true)
	{
		if (!$this->getIsNewRecord())
		{
			Yii::trace(get_class($this) . '.delete()', 'CmsActiveRecord');

			if ($this->beforeDelete($soft))
			{
				$result = $this->deleteByPk($this->getPrimaryKey()) > 0;
				$this->afterDelete();
				return $result;
			}
			else
				return false;
		}
		else
			throw new CDbException('The active record cannot be deleted because it is new.');
	}

	/**
	 * Returns whether the record is soft-deleted.
	 * @return boolean the result
	 */
	public function getDeleted()
	{
		return $this->owner->hasAttribute('deleted') ? $this->deleted === '1' : false;
	}
}
