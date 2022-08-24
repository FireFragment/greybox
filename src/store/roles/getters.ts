import { Role } from 'src/types/role';
import { RolesState } from 'src/store/roles/state';
import { Getter } from 'vuex';
import { State } from 'src/store';
import { EventFull, EventPrice } from 'src/types/event';
import { $isPDS } from 'boot/custom';
import { translationMatchesInAnyLanguage } from 'boot/i18n';

export const role = (state: RolesState) => (id: number): Role | undefined => state.roles
  .find((item) => (item.id === id));

export const eventRoles: Getter<RolesState, State> = (
  state: RolesState, _, __, rootGetters,
) => (eventId: number, isIndividual: boolean = true): Role[] => {
  // eslint-disable-next-line max-len
  // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-assignment,@typescript-eslint/no-unsafe-member-access
  const event: EventFull = rootGetters['events/fullEvent'](eventId);

  // Check if roles are present in event's prices
  return state.roles.filter((r: Role) => (
    (
      r.id === Infinity // Is team role...
      && !isIndividual // ... registration type is not individual...
      && event.prices.find((price: EventPrice) => price.role.id === 1) // ...debater role is present
    ) || (
      event.prices.find((price: EventPrice) => price.role.id === r.id)
      && (!$isPDS || r.id !== 1) // Individual debater should be hidden on PDS
    )
  ));
};

export const roleFromSlug = (state: RolesState) => (
  slug: string,
): Role | undefined => state.roles.find((r: Role) => translationMatchesInAnyLanguage(r.slug, slug));
