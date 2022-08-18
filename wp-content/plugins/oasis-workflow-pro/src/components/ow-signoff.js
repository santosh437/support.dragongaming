/**
 * WordPress Dependencies
 */
const { __ } = wp.i18n;
const { pick } = lodash;
const { Fragment, Component, createRef } = wp.element;
const { compose } = wp.compose;
const { PanelBody, PanelRow, SelectControl, Dropdown, TextareaControl, Button, Spinner } = wp.components;
const { withSelect, withDispatch } = wp.data;

/**
 * Internal dependencies
 */
import TaskPriorities from "./ow-task-priority-select-control";
import OWDueDatePicker from "./ow-due-date-picker";
import OWDueDateLabel from "./ow-due-date-label";
import SignoffLastStep from "./ow-signoff-last-step";
import PrePublishChecklist from "./ow-pre-publish-checklist";
import { getActionHistoryIdFromURL, getTaskUserFromURL, getSignOffActions, getStepAssignees } from "../util";
import HelpImage from "../images/help.png";

export class Signoff extends Component {
    constructor() {
        super(...arguments);

        this.signoffPanelRef = createRef();

        this.state = {
            signoffButtonText: __("Sign Off", "oasisworkflow"),
            assignActorLabel: __("Assign Actor(s)", "oasisworkflow"),
            dueDateLabel: __("Due Date", "oasisworkflow"),
            continueToSignoffText: __("Continue to Signoff", "oasisworkflow"),
            displayDueDate: "",
            mandatoryComments: "",
            actions: [],
            selectedAction: "",
            signoffSteps: [{ label: "", value: "" }],
            selectedStep: "",
            selectedPriority: "2normal",
            assignee: [],
            selectedAssignees: [],
            assignToAll: false,
            actionHistoryId: getActionHistoryIdFromURL(),
            taskUser: getTaskUserFromURL(),
            comments: "",
            isLastStep: false,
            lastStepDecision: "success",
            validationErrors: [],
            errorType: "",
            byPassWarning: false,
            selectedTeam: "",
            articleChecklist: [],
            selectedChecklist: [],
            redirectingLoader: "hide",
            stepSpinner: "hide",
            assigneeSpinner: "hide",
            submitSpinner: "hide",
            submitButtonDisable: false
        };
    }

    componentDidMount() {
        let customWorkflowTerminology = this.props.owSettings.terminology_settings.oasiswf_custom_workflow_terminology;
        let workflowSettings = this.props.owSettings.workflow_settings;

        if (customWorkflowTerminology) {
            let signoffButtonText = customWorkflowTerminology.signOffText;
            let assignActorLabel = customWorkflowTerminology.assignActorsText;
            let dueDateLabel = customWorkflowTerminology.dueDateText;
            let continueToSignoffText = customWorkflowTerminology.continueToSignoffText;
            this.setState({
                signoffButtonText,
                assignActorLabel,
                dueDateLabel,
                continueToSignoffText
            });
        }

        if (workflowSettings) {
            let displayDueDate = workflowSettings.oasiswf_default_due_days;
            let mandatoryComments = workflowSettings.oasiswf_comments_setting;
            // set the default due date by using the workflow settings
            let dueDate = new Date();
            if (displayDueDate !== "") {
                dueDate.setDate(dueDate.getDate() + parseInt(displayDueDate));
            }
            this.props.setDueDate({ dueDate: dueDate });
            this.setState({
                displayDueDate,
                mandatoryComments
            });
        }

        // fetch step action details - essentially, show review actions or assignment/publish actions
        wp.apiFetch({
            path: "/oasis-workflow/v1/workflows/signoff/stepActions/actionHistoryId=" + this.state.actionHistoryId,
            method: "GET"
        }).then(
            (step_decision) => {
                let success_action = step_decision.success_action;
                let failure_action = step_decision.failure_action;
                this.setState({
                    actions: getSignOffActions(success_action, failure_action)
                });
            },
            (err) => {
                console.log(err);
                return err;
            }
        );
    }

    getSignoffSteps(stepDecision) {
        let postId = this.props.postId;
        let decision = "success";

        // Set selected stepDecision
        this.setState({
            selectedAction: stepDecision,
            stepSpinner: "show"
        });

        if ("complete" === stepDecision) {
            decision = "success";
        }
        if ("unable" === stepDecision) {
            decision = "failure";
        }
        // get next steps depending on the step/task decision
        wp.apiFetch({
            path:
                "/oasis-workflow/v1/workflows/signoff/nextSteps/actionHistoryId=" +
                this.state.actionHistoryId +
                "/decision=" +
                decision +
                "/postId=" +
                postId,
            method: "GET"
        }).then(
            (stepdata) => {
                if (stepdata.steps === "") {
                    // this is the last step, and so, we didn't get any next steps
                    this.setState({
                        isLastStep: true,
                        lastStepDecision: decision,
                        stepSpinner: "hide"
                    });
                } else {
                    this.setState({
                        isLastStep: false,
                        lastStepDecision: "success"
                    });
                    let steps = stepdata.steps.map((step) => pick(step, ["step_id", "step_name"]));
                    let signoffSteps = [];

                    // if there is more than one possible next step
                    if (steps.length !== 1) {
                        signoffSteps.push({
                            label: __("Select Step", "oasisworkflow"),
                            value: ""
                        });
                    }

                    steps.map((step) => {
                        signoffSteps.push({ label: step.step_name, value: step.step_id });
                    });

                    this.setState({
                        signoffSteps: signoffSteps,
                        stepSpinner: "hide"
                    });

                    // if there is only one possible next step, auto select it
                    if (steps.length == 1) {
                        this.getSelectedStepDetails(signoffSteps[0]["value"]);
                        this.setState({
                            selectedStep: signoffSteps[0]["value"]
                        });
                    }
                }

                let customData = stepdata.custom_data;
                let allChecklist = [];
                // Get checklist
                if (customData.length !== 0) {
                    let checklistData = customData.map((checklist) => pick(checklist, ["condition", "value"]));
                    checklistData.map((checklist) => {
                        allChecklist.push({
                            label: checklist.condition,
                            value: checklist.value
                        });
                    });
                }
                this.setState({
                    articleChecklist: allChecklist
                });

                return stepdata;
            },
            (err) => {
                console.log(err);
                return err;
            }
        );
    }

    /**
     * For the selected step, get other details, like assignee list, assignToAll flag etc
     *
     * @param {Integer} stepId
     */
    getSelectedStepDetails(stepId) {
        let postId = this.props.postId;
        this.setState({
            selectedStep: stepId,
            assigneeSpinner: "show"
        });
        wp.apiFetch({
            path:
                "/oasis-workflow/v1/workflows/signoff/stepDetails/actionHistoryId=" +
                this.state.actionHistoryId +
                "/stepId=" +
                stepId +
                "/postId=" +
                postId,
            method: "GET"
        }).then(
            (stepdata) => {
                let errors = [];
                let availableAssignees = [];
                let assignToAll = stepdata.assign_to_all === 1 ? true : false;
                let teamId = stepdata.team_id;

                if (teamId !== "") {
                    this.handleTeamChange(teamId, stepId);
                }

                this.props.setDueDate({ dueDate: stepdata.due_date });

                // If not set team than display step assignee
                if (teamId == "") {
                    let assignees = stepdata.users;
                    // Display Validation Message if no user found for the step
                    if (assignees.length === 0) {
                        errors.push(__("No users found to assign the task.", "oasisworkflow"));
                        this.setState({
                            validationErrors: errors,
                            assignee: []
                        });
                        // scroll to the top, so that the user can see the error
                        this.signoffPanelRef.current.scrollIntoView();
                        return;
                    }

                    // Set and Get Assignees from the util function
                    let stepAssignees = getStepAssignees({
                        assignees: assignees,
                        assignToAll: assignToAll
                    });
                    availableAssignees = stepAssignees.availableAssignees;

                    this.setState({
                        selectedAssignees: stepAssignees.selectedAssignees
                    });
                }

                this.setState({
                    assignee: availableAssignees,
                    assignToAll: assignToAll,
                    assigneeSpinner: "hide"
                });
                return stepdata;
            },
            (err) => {
                console.log(err);
                return err;
            }
        );
    }

    /**
     * handle priority change
     * @param {*} selectedPriority
     */
    handleOnPriorityChange(selectedPriority) {
        this.setState({
            selectedPriority
        });
    }

    /**
     * Handle Team Change
     * @param {*} selectedTeam
     * @param {*} firstStepId
     */
    handleTeamChange(selectedTeam, slectedStepId = 0) {
        let postId = this.props.postId;

        let stepId = slectedStepId;
        if (slectedStepId == 0) {
            stepId = this.state.selectedStep;
        }

        this.setState({
            selectedTeam
        });

        wp.apiFetch({
            path:
                "/oasis-workflow/v1/workflows/teams/members/teamId=" +
                selectedTeam +
                "/postId=" +
                postId +
                "/stepId=" +
                stepId,
            method: "GET"
        }).then(
            (teamdata) => {
                let errors = [];
                let assignees = teamdata.users;
                // Display Validation Message if no user found for the selected team
                if (assignees.length === 0) {
                    errors.push(teamdata.errorMessage);
                    this.setState({
                        validationErrors: errors,
                        assignee: []
                    });
                    // scroll to the top, so that the user can see the error
                    this.signoffPanelRef.current.scrollIntoView();
                    return;
                }

                // Set and Get Assignees from the store function
                let stepAssignees = getStepAssignees({
                    assignees: assignees,
                    assignToAll: this.state.assignToAll
                });

                this.setState({
                    assignee: stepAssignees.availableAssignees,
                    selectedAssignees: stepAssignees.selectedAssignees
                });
            },
            (err) => {
                console.log(err);
                return err;
            }
        );
    }

    /**
     * Handle selected pre publish checklist
     * @param {*} checklist
     */
    selectPrePublishChecklist(checklist) {
        // Get current state
        const options = this.state.selectedChecklist;
        let index;

        // check if the check box is checked or unchecked
        if (checklist.target.checked) {
            options.push(checklist.target.value);
        } else {
            index = options.indexOf(checklist.target.value);
            options.splice(index, 1);
        }
        this.setState({
            selectedChecklist: options
        });
    }

    /**
     * validate sign off
     * @param {Object} data
     */
    validateSignoff(data) {
        const errors = [];
        let current_date = new Date();
        current_date = moment(current_date).format("YYYY-MM-DD");
        let due_date = moment(data.due_date).format("YYYY-MM-DD");

        if (data.step_id === "") {
            errors.push(__("Please select a step.", "oasisworkflow"));
        }

        if (data.due_date === "") {
            errors.push(__("Please enter a due date.", "oasisworkflow"));
        }

        if (data.due_date !== "" && moment(current_date).isAfter(due_date) == true) {
            errors.push(__("Due date must be greater than the current date.", "oasisworkflow"));
        }

        if (data.assignees.length === 0 && !this.state.assignToAll) {
            errors.push(__("No assigned actor(s).", "oasisworkflow"));
        }

        if (data.comments === "" && this.state.mandatoryComments === "mandatory") {
            errors.push(__("Please enter comments.", "oasisworkflow"));
        }

        return errors;
    }

    /**
     * The user wants to continue to sign off, so we need to bypass the warnings
     * @param {*} event
     */
    handleContinueToSignoff(event) {
        // call handleSignoff as callback of setState, so that it's called after the state is set
        this.setState(
            {
                byPassWarning: true
            },
            () => {
                this.handleSignoff();
            }
        );
    }

    /**
     * handle form submit for sign off
     */
    handleSignoff() {
        // event.preventDefault();

        this.setState({
            submitSpinner: "show",
            submitButtonDisable: true
        });

        let form_data = {
            post_id: this.props.postId,
            step_id: this.state.selectedStep,
            decision: this.state.selectedAction,
            priority: this.state.selectedPriority,
            assignees: this.state.selectedAssignees,
            due_date: this.props.dueDate,
            comments: this.state.comments,
            history_id: this.state.actionHistoryId,
            hideSignOff: false,
            task_user: this.state.taskUser,
            team_id: this.state.selectedTeam,
            assign_to_all: this.state.assignToAll,
            pre_publish_checklist: this.state.selectedChecklist,
            by_pass_warning: this.state.byPassWarning
        };

        // save the post
        this.props.onSave();

        const errors = this.validateSignoff(form_data);

        if (errors.length > 0) {
            this.setState({
                validationErrors: errors,
                submitSpinner: "hide",
                submitButtonDisable: false,
                errorType: ""
            });

            // scroll to the top, so that the user can see the error
            this.signoffPanelRef.current.scrollIntoView();

            return;
        }

        this.setState({
            validationErrors: []
        });

        // TODO: introducing a delay to allow the post to be saved and then invoke the sign off
        var that = this;
        setTimeout(function () {
            that.invokeSignoffAPI(form_data);
        }, 500);
    }

    invokeSignoffAPI(form_data) {
        wp.apiFetch({
            path: "/oasis-workflow/v1/workflows/signoff/",
            method: "POST",
            data: form_data
        }).then(
            (submitResponse) => {
                if (submitResponse.success_response == false) {
                    this.setState({
                        validationErrors: submitResponse.validation_error,
                        errorType: submitResponse.error_type,
                        submitSpinner: "hide",
                        submitButtonDisable: false
                    });
                    // scroll to the top, so that the user can see the error
                    this.signoffPanelRef.current.scrollIntoView();
                } else {
                    if (submitResponse.new_action_history_id != this.state.actionHistoryId) {
                        this.setState({
                            hideSignOff: true,
                            redirectingLoader: "show"
                        });
                    }
                    // Redirect user to inbox page
                    if (submitResponse.redirect_link !== "") {
                        window.location.href = submitResponse.redirect_link;
                    } else {
                        this.props.handleResponse(submitResponse);
                    }
                }
                return submitResponse;
            },
            (err) => {
                console.log(err);
                return err;
            }
        );
    }

    render() {
        const { isSaving, isPostInWorkflow, isCurrentPostPublished } = this.props;
        const {
            validationErrors,
            errorType,
            isLastStep,
            lastStepDecision,
            hideSignOff,
            signoffButtonText,
            continueToSignoffText,
            assignActorLabel,
            dueDateLabel,
            displayDueDate,
            articleChecklist,
            redirectingLoader,
            stepSpinner,
            assigneeSpinner,
            submitSpinner,
            submitButtonDisable
        } = this.state;

        if (hideSignOff && redirectingLoader === "show") {
            return (
                <div>
                    <PanelBody>{__("redirecting...", "oasisworkflow")}</PanelBody>
                </div>
            );
        }

        // post is not in workflow anymore, so return empty
        if (!isPostInWorkflow || hideSignOff || isCurrentPostPublished) {
            return "";
        }

        return (
            <PanelBody ref={this.signoffPanelRef} initialOpen={true} title={signoffButtonText}>
                <form className="reusable-block-edit-panel">
                    {validationErrors.length !== 0 ? (
                        <div id="owf-error-message" className="notice notice-error is-dismissible">
                            {validationErrors.map((error) => (
                                <p key={error}>{error}</p>
                            ))}
                            {errorType == "warning" ? (
                                <p>
                                    <Button isSecondary focus="true" onClick={this.handleContinueToSignoff.bind(this)}>
                                        {continueToSignoffText}
                                    </Button>
                                </p>
                            ) : (
                                ""
                            )}
                        </div>
                    ) : (
                        ""
                    )}
                    <label>
                        {__("Action", "oasisworkflow") + ": "}
                        <a
                            href="#"
                            title={__(
                                "After completing or reviewing the post/article, select appropriate action.",
                                "oasisworkflow"
                            )}
                            className="tooltip"
                        >
                            <span title="">
                                <img src={HelpImage} className="help-icon" />
                            </span>
                        </a>
                    </label>
                    <SelectControl
                        value={this.state.selectedAction}
                        options={this.state.actions}
                        onChange={this.getSignoffSteps.bind(this)}
                    />
                    {isLastStep ? (
                        <SignoffLastStep
                            stepDecision={lastStepDecision}
                            handleResponse={this.props.handleResponse}
                            checklist={articleChecklist}
                        />
                    ) : (
                        <div>
                            <div className="owf-spinner">{stepSpinner == "show" ? <Spinner /> : ""}</div>
                            <label>
                                {__("Step", "oasisworkflow") + ": "}
                                <a
                                    href="#"
                                    title={__(
                                        "Your action will push the Post/Article to the below listed next step.",
                                        "oasisworkflow"
                                    )}
                                    className="tooltip"
                                >
                                    <span title="">
                                        <img src={HelpImage} className="help-icon" />
                                    </span>
                                </a>
                            </label>
                            <SelectControl
                                value={this.state.selectedStep}
                                options={this.state.signoffSteps}
                                onChange={this.getSelectedStepDetails.bind(this)}
                            />
                            <TaskPriorities
                                value={this.state.selectedPriority}
                                onChange={this.handleOnPriorityChange.bind(this)}
                            />
                            <div>
                                <div className="owf-spinner">
                                    {assigneeSpinner == "show" && this.state.assignToAll == false ? <Spinner /> : ""}
                                </div>
                                {!this.state.assignToAll ? (
                                    <SelectControl
                                        multiple
                                        className="ow-multi-select"
                                        label={assignActorLabel + ":"}
                                        value={this.state.selectedAssignees}
                                        options={this.state.assignee}
                                        onChange={(selectedAssignees) => this.setState({ selectedAssignees })}
                                    />
                                ) : (
                                    ""
                                )}
                            </div>
                            {articleChecklist.length !== 0 ? (
                                <PrePublishChecklist
                                    checklist={articleChecklist}
                                    onChange={this.selectPrePublishChecklist.bind(this)}
                                />
                            ) : (
                                ""
                            )}
                            {displayDueDate !== "" ? (
                                <PanelRow className="edit-post-post-schedule">
                                    <label>{dueDateLabel + ":"} </label>
                                    <Dropdown
                                        position="bottom left"
                                        contentClassName="edit-post-post-schedule__dialog"
                                        renderToggle={({ onToggle, isOpen }) => (
                                            <Fragment>
                                                <Button
                                                    type="button"
                                                    onClick={onToggle}
                                                    aria-expanded={isOpen}
                                                    aria-live="polite"
                                                    isLink
                                                >
                                                    <OWDueDateLabel />
                                                </Button>
                                            </Fragment>
                                        )}
                                        renderContent={() => <OWDueDatePicker />}
                                    />
                                </PanelRow>
                            ) : (
                                ""
                            )}
                            <PanelRow>
                                <label>
                                    {__("Comments", "oasisworkflow") + ": "}
                                    <a
                                        href="#"
                                        title={__(
                                            "The comments will be visible throughout the workflow.",
                                            "oasisworkflow"
                                        )}
                                        className="tooltip"
                                    >
                                        <span title="">
                                            <img src={HelpImage} className="help-icon" />
                                        </span>
                                    </a>
                                </label>
                            </PanelRow>
                            <PanelRow className="panel-without-label">
                                <TextareaControl
                                    value={this.state.comments}
                                    onChange={(comments) => this.setState({ comments })}
                                />
                            </PanelRow>
                            <PanelRow>
                                <Button
                                    isPrimary
                                    isBusy={isSaving}
                                    focus="true"
                                    disabled={submitButtonDisable}
                                    onClick={this.handleSignoff.bind(this)}
                                >
                                    {signoffButtonText}
                                </Button>
                                <div className="owf-spinner">{submitSpinner == "show" ? <Spinner /> : ""}</div>
                            </PanelRow>
                        </div>
                    )}
                </form>
            </PanelBody>
        );
    }
}

export default compose([
    withSelect((select) => {
        const { getCurrentPostId, getEditedPostAttribute, isCurrentPostPublished } = select("core/editor");
        const { getDueDate, getOWSettings, getPostInWorkflow } = select("plugin/oasis-workflow");
        return {
            postId: getCurrentPostId(),
            postMeta: getEditedPostAttribute("meta"),
            dueDate: getDueDate(),
            owSettings: getOWSettings(),
            isPostInWorkflow: getPostInWorkflow(),
            isCurrentPostPublished: isCurrentPostPublished()
        };
    }),
    withDispatch((dispatch) => ({
        onSave: dispatch("core/editor").savePost,
        setDueDate: dispatch("plugin/oasis-workflow").setDueDate
    }))
])(Signoff);
