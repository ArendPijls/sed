<?php

/**
 * SpotlightEdit
 *
 * This is the edit-action, it will display a form to edit an existing spotlight item
 *
 * @package		backend
 * @subpackage	spotlight
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class SpotlightEdit extends BackendBaseActionEdit
{
	/**
	 * Datagrid with the revisions
	 *
	 * @var	BackendDataGridDB
	 */
	private $dgRevisions;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exists
		if(BackendSpotlightModel::exists($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get all data for the item we want to edit
			$this->getData();

			// load the datagrid with revisions
			$this->loadRevisions();

			// load the form
			$this->loadForm();

			// validate the form
			$this->validateForm();

			// parse the datagrid
			$this->parse();

			// display the page
			$this->display();
		}

		// no item found, throw an exceptions, because somebody is fucking with our url
		else $this->redirect(BackendModel::createURLForAction('index') .'?error=non-existing');
	}


	private function getData()
	{
		// get the record
		$this->record = (array) BackendSpotlightModel::get($this->id);

		// is there a revision specified?
		$revisionToLoad = $this->getParameter('revision', 'int');

		// if this is a valid revision
		if($revisionToLoad !== null)
		{
			// overwrite the current record
			$this->record = (array) BackendSpotlightModel::getRevision($this->id, $revisionToLoad);

			// show warning
			$this->tpl->assign('usingRevision', true);
		}
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('edit');

		// create elements
		$this->frm->addTextField('title', $this->record['title']);
		$this->frm->addEditorField('content', $this->record['content']);
		$this->frm->addCheckBox('hidden', (bool) $this->record['hidden'] == 'Y');
		$this->frm->addButton('submit', ucfirst(BL::getLabel('Edit')), 'submit');
	}


	/**
	 * Load the datagrid with revisions
	 *
	 * @return	void
	 */
	private function loadRevisions()
	{
		// create datagrid
		$this->dgRevisions = new BackendDataGridDB(BackendSpotlightModel::QRY_BROWSE_REVISIONS, array('archived', $this->record['id']));

		// hide columns
		$this->dgRevisions->setColumnsHidden(array('id', 'revision_id'));

		// disable paging
		$this->dgRevisions->setPaging();

		// set headers
		$this->dgRevisions->setHeaderLabels(array('title' => BL::getLabel('Title'), 'edited_on' => BL::getLabel('LastEditedOn')));

		// set column-functions
		$this->dgRevisions->setColumnFunction(array('BackendDataGridFunctions', 'getLongDate'), array('[edited_on]'), 'edited_on', true);

		// add use column
		$this->dgRevisions->addColumn('use', null, BL::getLabel('UseThisVersion'), BackendModel::createURLForAction('edit') .'?id=[id]&revision=[revision_id]', BL::getLabel('Edit'));
	}


	/**
	 * Parse the form
	 *
	 * @return	void
	 */
	protected function parse()
	{
		// call parent
		parent::parse();

		$this->tpl->assign('id', $this->record['id']);
		$this->tpl->assign('title', $this->record['title']);
		$this->tpl->assign('revision_id', $this->record['revision_id']);

		// assign revisions-datagrid
		$this->tpl->assign('revisions', ($this->dgRevisions->getNumResults() != 0) ? $this->dgRevisions->getContent() : false);


	}


	/**
	 * Validate the form
	 *
	 * @return	void
	 */
	private function validateForm()
	{
		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// validate fields
			$this->frm->getField('title')->isFilled(BL::getError('TitleIsRequired'));
			$this->frm->getField('content')->isFilled(BL::getError('ContentIsRequired'));

			// no errors?
			if($this->frm->getCorrect())
			{
				// get values
				$aValues = (array) $this->frm->getValues();

				// insert the item
				$id = (int) BackendSpotlightModel::update($this->id, $aValues);

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') .'?report=edit&var='. $aValues['title'] .'&hilight=id-'. $id);
			}
		}
	}
}

?>