<?php

namespace App\Enums;

enum SettingKeys: string
{
    case CONTACT_PHONE = 'contact_phone';
    case AGREEMENT_POLICY = 'agreement_policy';
    case PRIVACY_POLICY = 'privacy_policy';
    case CONSENT_FORM_DESCRIPTION = 'consent_form_description';
}
