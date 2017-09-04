<?php
namespace App\Presenters;

use Nette;
use Drahak\Restful\IResource;
use Drahak\Restful\Application\UI\ResourcePresenter;
use Drahak\Restful\Validation\IValidator;

class RolePresenter extends ResourcePresenter
{
  /** @var Nette\Database\Context */
  private $database;

  protected $typeMap = array(
    'json' => IResource::JSON,
    'xml' => IResource::XML
  );

  public function __construct(Nette\Database\Context $database)
  {
    $this->database = $database;
  }

  public function validateCreate()
  {
    return $this->validate($this->getInput());
  }

  public function actionCreate()
  {
    $input = $this->getInput();
    $role = $this->database->table('role')->insert([
      'name' => $input->name,
      'value' => $input->value
    ]);
    return $this->resource->role = $role;
  }

  public function actionRead($id)
  {
    if (isset($id))
    {
      $role = $this->database->table('role')->get($id);
      return $this->resource->role = $role;
    }
    $roles = $this->database->table('role');
    return $this->resource->roles = $roles;
  }

  public function validateUpdate($id)
  {
    $input = $this->getInput();
    return $this->validate($input);    
  }

  public function actionUpdate($id)
  {
    $input = $this->getInput();
    $role = $this->database->table('role')->get($id);
    if ($role) $role->update($input);
    return $this->resource->role = $role;
  }

  public function actionDelete($id)
  {
    $role = $this->database->table('role')->get($id);
    $role->delete();
    return $this->resource->role = $role;
  }

  // TODO: check the values range for tinyint
  private function validate($input)
  {
    $input->field('name')
      ->addRule(IValidator::MAX_LENGTH, "Maximum name length is %d.", 30);
    $input->field('value')
      ->addRule(IValidator::RANGE, "Role value has to be between %s and %s.", [1, 100]);    
  }

}