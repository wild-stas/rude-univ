<?

namespace rude;

class faculties
{
	public static function get($id = null)
	{
		$q = new query(RUDE_DATABASE_TABLE_FACULTIES);

		if ($id !== null)
		{
			$q->where(RUDE_DATABASE_FIELD_ID, (int) $id);
			$q->query();

			return $q->get_object();
		}

		$q->query();

		return $q->get_object_list();
	}

	public  static function get_by_shortname($shortname)
	{
		$q = new query(RUDE_DATABASE_TABLE_FACULTIES);
		$q->where(RUDE_DATABASE_FIELD_SHORTNAME,$shortname);
		$q->query();
		return $q->get_object();
	}

	public static function remove($id)
	{
		if (static::is_exists($id))
		{
			$q = new dquery(RUDE_DATABASE_TABLE_FACULTIES);
			$q->where(RUDE_DATABASE_FIELD_ID, (int) $id);
			$q->query();

			return $q->affected();
		}

		return false;
	}

	public static function is_exists($id)
	{
		if (static::get($id))
		{
			return true;
		}

		return false;
	}

	public static function add($name,$shortname)
	{
		$q = new cquery(RUDE_DATABASE_TABLE_FACULTIES);
		$q->add(RUDE_DATABASE_FIELD_NAME,$name);
		$q->add(RUDE_DATABASE_FIELD_SHORTNAME,$shortname);
		$q->query();

		return true;
	}

	public static function edit($id,$name,$shortname)
	{
		$q = new uquery(RUDE_DATABASE_TABLE_FACULTIES);
		$q->update(RUDE_DATABASE_FIELD_NAME,$name);
		$q->update(RUDE_DATABASE_FIELD_SHORTNAME,$shortname);
		$q->where(RUDE_DATABASE_FIELD_ID,$id);
		$q->query();

		return true;
	}
}