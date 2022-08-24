import { Role } from 'src/types/role';
import { RolesState } from 'src/store/roles/state';
import { Getter } from 'vuex';
import { State } from 'src/store';
import { EventFull, EventPrice } from 'src/types/event';
import { $isPDS, $slugTranslation, $tr } from 'boot/custom';

export const role = (state: RolesState) => (id: number): Role | undefined => state.roles
  .find((item) => (item.id === id));

export const eventRoles: Getter<RolesState, State> = (
  state: RolesState, _, __, rootGetters,
) => (eventId: number, isIndividual: boolean = true): Role[] => {
  // eslint-disable-next-line max-len
  // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-assignment,@typescript-eslint/no-unsafe-member-access
  const event: EventFull = rootGetters['events/fullEvent'](eventId);

  // Check if roles are present in event's prices
  const result: Role[] = state.roles.filter((r: Role) => (
    event.prices.find((price: EventPrice) => price.role.id === r.id)
    && (!$isPDS || r.id !== 1) // Individual debater should be hidden on PDS
  ));

  // Debater role is present -> push team role
  if (!isIndividual && result.find((r: Role) => r.id === 1)) {
    const teamName = {
      id: Infinity,
      created_at: '',
      updated_at: '',
      cs: <string>$tr('event.types.team', null, false, 'cs'),
      en: <string>$tr('event.types.team', null, false, 'en'),
    };

    result.unshift({
      id: Infinity,
      name: teamName,
      slug: $slugTranslation(teamName),
      icon: 'users',
      created_at: '',
      updated_at: '',
    });
  }

  return result;
};
