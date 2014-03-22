<?php
/**
 * CmsMigrateCommand class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; 2012, Nord Software Ltd
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package cms.commands
 * @since 2.0.0
 */

Yii::import('system.cli.commands.MigrateCommand');

/**
 * Migrate command that allows for cms database migration.
 */
class CmsMigrateCommand extends MigrateCommand
{
	/**
	 * @var string the directory that stores the migrations.
	 */
	public $migrationPath = 'cms.migrations';
	/**
	 * @var string the name of the table for keeping applied migration information.
	 */
	public $migrationTable = 'cms_migration';
}
