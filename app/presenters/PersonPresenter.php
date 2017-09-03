<?php
namespace App\Presenters;

use Nette;
use Drahak\Restful\IResource;
use Drahak\Restful\Application\UI\ResourcePresenter;
use Drahak\Restful\Validation\IValidator;

class PersonPresenter extends ResourcePresenter
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

  /*
   * @PATCH person/<id>/badge
   */
  public function actionBadge($id)
  {
    $person = $this->database->table('clovek')->get($id);
    if ($person) $person->update([
      'received_badge' => 1
    ]);
    return $this->resource->person = $person;
  }

  public function actionRead($id)
  {
    if (isset($id))
    {
      $person = $this->database->table('clovek')->get($id);
      return $this->resource->person = $person;
    }
    $people = $this->database->table('clovek')->where('received_badge', false)->order('badge DESC');
    return $this->resource->people = $people;
        if(false)
{
    echo '<a href="?check">Check</a><br><br>';
  echo '<table><thead><td><strong>jméno</strong></td><td><strong>klub</strong></td><td><strong>odznáček</strong></td><td><strong>dostal</strong></td></thead>';
  $lide = mysql_query("SELECT clovek_ID, jmeno, prijmeni, odznak, klub_ID FROM clovek WHERE dostal = 0 AND clen = 1 ORDER BY odznak DESC, jmeno, prijmeni ASC");
    while ($clovek = mysql_fetch_array($lide))
  {
     $klub_ID = $clovek['klub_ID'];
     $klub = mysql_fetch_array(mysql_query("SELECT kratky_nazev FROM klub WHERE klub_ID = '$klub_ID'"));
     echo "<tr><td>$clovek[jmeno] $clovek[prijmeni]</td><td>$klub[kratky_nazev]</td><td>$clovek[odznak]</td><td><a href=\"?ano=$clovek[clovek_ID]\">ano</a></td></tr>";
  }
  echo '</table>';
}
  }




}