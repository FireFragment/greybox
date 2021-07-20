import Vue from "vue";

import "./styles/quasar.styl";
import lang from "quasar/lang/cs.js";
import "@quasar/extras/fontawesome-v5/fontawesome-v5.css";
import "@quasar/extras/material-icons/material-icons.css";
import Quasar, {
  QAvatar,
  QBanner,
  QBtn,
  QBtnDropdown,
  QCard,
  QCardActions,
  QCardSection,
  QCheckbox,
  QDialog,
  QDrawer,
  QForm,
  QHeader,
  QIcon,
  QInnerLoading,
  QInput,
  QItem,
  QItemLabel,
  QItemSection,
  QLayout,
  QList,
  QPage,
  QPageContainer,
  QScrollArea,
  QSelect,
  QSeparator,
  QSpace,
  QSpinner,
  QToolbar,
  QToolbarTitle,
  QTooltip
} from "quasar";

Vue.use(Quasar, {
  components: {
    QAvatar,
    QBanner,
    QBtn,
    QBtnDropdown,
    QCard,
    QCardActions,
    QCardSection,
    QCheckbox,
    QDialog,
    QDrawer,
    QForm,
    QHeader,
    QIcon,
    QInnerLoading,
    QInput,
    QItem,
    QItemLabel,
    QItemSection,
    QLayout,
    QList,
    QPage,
    QPageContainer,
    QScrollArea,
    QSelect,
    QSeparator,
    QSpace,
    QSpinner,
    QToolbar,
    QToolbarTitle,
    QTooltip
  },
  config: {
    loadingBar: {
      color: "light-blue-5"
    }
  },
  lang: lang
});
