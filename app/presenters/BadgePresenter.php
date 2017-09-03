<?php
//namespace ResourcesModule;
namespace App\Presenters;

use Nette;
use Drahak\Restful\IResource;
use Drahak\Restful\Application\UI\ResourcePresenter;
use Drahak\Restful\Validation\IValidator;

class BadgePresenter extends ResourcePresenter
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
    $badge = $this->database->table('badge')->insert([
      'name' => $input->name,
      'requiredIB' => $input->requiredIB
    ]);
    return $this->resource->badge = $badge;
  }

  public function actionRead($id)
  {
    if (isset($id))
    {
      $badge = $this->database->table('badge')->get($id);
      return $this->resource->badge = $badge;
    }
    $badge = $this->database->table('badge');
    return $this->resource->badges = $badge;
  }

  public function validateUpdate($id)
  {
    $input = $this->getInput();
    return $this->validate($input);    
  }

  public function actionUpdate($id)
  {
    $input = $this->getInput();
    $badge = $this->database->table('badge')->get($id);
    if ($badge) $badge->update($input);
    return $this->resource->badge = $badge;
  }

  public function actionDelete($id)
  {
    $badge = $this->database->table('badge')->get($id);
    $badge->delete();
    return $this->resource->badge = $badge;
  }

  /*
   * @PATCH badge/check
   */
  public function actionCheck()
  {
    $badges = $this->database->table('badge')->fetchAll();
    usort($badges, function ($a, $b)
    {
      if ($a->requiredIB == $b->requiredIB) return 0;
      if ($a->requiredIB < $b->requiredIB) return -1;
      else return 1;
    });

    $people = $this->database->table('clovek')->where('received_badge', 1);
    $updatedPeople = array();
    foreach ($people as $person)
    {
      $debatePoints = $bonusPoints = 0;
      $debatePoints = $this->database->table('clovek_debata_ibody')->where('clovek_ID', $person->clovek_ID)->sum('ibody');
      $bonusPoints = $this->database->table('clovek_ibody')->where('clovek_ID', $person->clovek_ID)->sum('ibody');
      $totalPoints = $debatePoints + $bonusPoints;

      $newBadge = null;
      foreach ($badges as $badge)
      {
        if ($totalPoints >= $badge->requiredIB) $newBadge = $badge;
        else break;
      }

      if (!is_null($newBadge) AND $person->badge != $newBadge->id)
      {
        $person->update([
          'badge' => $newBadge,
          'received_badge' => 0
        ]);
        $updatedPeople[] = $person;
      }
    }

    return $this->resource->updatedPeople = $updatedPeople;
  }

  private function validate($input)
  {
    $input->field('name')
      ->addRule(IValidator::MAX_LENGTH, "Maximum name length is %d.", 30);
    $input->field('requiredIB')
      ->addRule(IValidator::RANGE, "Required IB has to be between %s and %s.", [0, 30000]);    
  }

}