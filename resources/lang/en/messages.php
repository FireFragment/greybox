<?php

return [
    'hello' => 'Hello',
    'best' => 'Best regards',
    'cda' => 'Czech Debate Association',

    'password.reset.subject' => 'Greybox - password recovery',
    'password.reset.before_link' => "We have received your request for password recovery. To create a new password, use the following link",
    'password.reset.link' =>  'password-reset',
    'password.reset.after_link' => 'The link is valid for the next 24 hours. If you don\'t use it in time, please request the password recovery again.',
    'password.reset.not_required' => 'If you haven\'t requested password recovery, you can ignore this email.',

    'registration.confirmation' => 'Registration confirmation',
    'registration.confirmation.before_event_name' => 'we have received your registration for', // TODO: return ' the' after PDS
    'registration.confirmation.after_event_name' => 'We are looking forward to seeing you soon.', // TODO: add number of days and/or place
    'registration.confirmation.before_list' => 'Participants',
    'registration.confirmation.before_price' => 'If you haven\'t done so yet, please send',
    'registration.confirmation.before_date' => 'before',
    'registration.confirmation.after_date' => 'to CDA\'s bank account. You can find the payment details in the attached invoice.',
    'registration.confirmation.mistake' => 'If you find any mistake, please reply to this email.',

    'team.rules.breach.warning.subject' => 'There has been a potential breach in rules for team registrations',
    'team.rules.breach.warning.body' => 'There has been a potential breach in rules for team registrations. Please take a look at these',

    'team.rules.breach.minimum' => 'Not enough team members.',
    'team.rules.breach.maximum' => 'Too many team members.',
    'team.rules.breach.missing_old_greybox' => 'Debater does not have old_greybox_id filled: ',
    'team.rules.breach.impossible_check.debaters' => 'Impossible to check the paragraph 2 rules for the following debaters: ',
    'team.rules.breach.shared_team1' => 'Debaters ',
    'team.rules.breach.shared_team2' => ' shared the following team in the past: ',
    'team.rules.breach.shared_teams2' => ' shared the following teams in the past: ',
    'team.rules.breach.shared_team3' => ' and now they are registered as ',
    'team.rules.breach.impossible_check.team' => 'Impossible to check the paragraph 3.1 and 3.2 rules.',
    'team.rules.breach.new_debaters' => 'Debaters were not members of this team in the past.',
    'team.rules.breach.finals.no_past_debaters' => 'No debaters in the previous tournaments.',
    'team.rules.breach.finals.new_debaters' => 'Debaters were not members of this team in the past: '
];