<?php namespace Constant\Toggl;

class TimeEntry
{
    protected $id = 0;
    protected $ticket;
    protected $description;
    protected $entry_date;
    protected $client;
    protected $project;
    protected $tags = [];
    protected $task;
    protected $logged = false;
    protected $billable = false;
    protected $duration = 0;

    /**
     * Constructor
     *
     * @param TogglService $service Access to the Toggl API
     * @param string $id            ID of this task
     * @param string $client        Client name for task
     * @param string $project       Name of project for task
     * @param string $description   Task detail
     */
    public function __construct(TogglService $service, $id, $client, $project, $description)
    {
        $this->service = $service;
        $this->id = $id;
        $this->client = $client;
        $this->project = $project;
        $this->description = $description;
    }

    /**
     * Returns the internal task ID
     *
     * @return string
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * Sets the internal task ID for this task.
     * Task is determineed by concatenating project and
     * task code
     *
     * @param string $task
     */
    public function setTask($task)
    {
        $taskCode = filter_var($task, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        if (strpos($taskCode, '-') !== false) {
            $taskCode = str_replace('-', '', $taskCode);
        }
        $taskCode = $this->project . $taskCode;
        $this->task = $taskCode;
    }

    /**
     * Returns the ticket number for the task
     *
     * @return string
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * Sets the ticket number for the task
     *
     * @param string $ticket
     */
    public function setTicket($ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get that task detail
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the task detail
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Return the client name
     *
     * @return string
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Sets the client name
     *
     * @param string $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * Return the project for the task
     *
     * @return string
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Sets the project
     *
     * @param string $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }

    /**
     * Returns an array of tags associated to the task
     *
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Sets the internal array of tags for the task
     *
     * @param array $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * Sets the duration timestamp of the task
     *
     * @param int $dur
     */
    public function setDurationTime($dur)
    {
        $this->duration = $dur / 60 / 1000 / 60;
    }

    /**
     * Returns the duration timestamp of the task
     *
     * @return int
     */
    public function getDurationTime()
    {
        return $this->duration * 60 * 60;
    }

    /**
     * Determines if the task has been logged against ticket system
     *
     * @return boolean
     */
    public function isLogged()
    {
        return $this->logged;
    }

    /**
     * Sets if the task has been logged against ticket system
     *
     * @param boolean $logged
     */
    public function setLogged($logged)
    {
        $this->logged = $logged;
    }

    /**
     * Determines if the task is a billable task
     *
     * @return boolean
     */
    public function isBillable()
    {
        return $this->billable;
    }

    /**
     * Sets if the task is a billable task
     *
     * @param boolean $billable
     */
    public function setBillable($billable)
    {
        $this->billable = $billable;
    }

    /**
     * Returns the duration of the task in hours
     *
     * @return float
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Sets the duration timestamp of the task in hours
     *
     * @param float $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * Retunrs the id of the task
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Adds a tag to the internal tag array
     *
     * @param string $tag
     */
    public function addTag($tag)
    {
        $this->tags[] = $tag;
        $this->processTags();
    }

    /**
     * Processes an array of tags to find the ticket number
     * as well as other internally used tags
     *
     * @param array $tags An array of tags to process
     */
    public function processTags($tags = null)
    {
        if (empty($tags)) {
            $tags = $this->tags;
        }
        foreach ($tags as $tag) {
            $this->tags[] = $tag;
            if (preg_match('/[A-Z]+\-[\d]+/', $tag)) {
                $this->setTicket($tag);
                continue;
            }
            if ($tag == 'Jira') {
                $this->setLogged(true);
            }
        }
        $this->tags = array_unique($this->tags);
    }

    /**
     * Calls the Toggl API to persist this time entry
     *
     * @return TimeEntry
     */
    public function save()
    {
        return $this->service->saveTimeEntry($this);
    }

    /**
     * Returns the entry date of this task
     *
     * @return mixed
     */
    public function getEntryDate()
    {
        return $this->entry_date;
    }

    /**
     * Sets the entry date of this task
     *
     * @param mixed $entry_date
     */
    public function setEntryDate($entry_date)
    {
        $this->entry_date = $entry_date;
    }
}
