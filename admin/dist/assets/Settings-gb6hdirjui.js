import { a as useQueryClient, r as reactExports, R as React, p as parseURL, B as Button, e as setNotification, f as getFetch, h as setSettings, i as useQuery, L as Loader, j as fetchSettings } from "../main-gb6hdirjui.js";
import { S as SortMenu, H as Ht, I as InputField } from "./datepicker-gb6hdirjui.js";
import { T as TextArea } from "./Textarea-gb6hdirjui.js";
import { S as Switch } from "./Switch-gb6hdirjui.js";
import { u as useMutation, M as MultiSelectMenu } from "./MultiSelectMenu-gb6hdirjui.js";
function SettingsOption({ settingId, option }) {
  const queryClient = useQueryClient();
  const { id, type, title, description, placeholder, value, possible_values } = option;
  const [date, setDate] = reactExports.useState(type !== "datetime" || new Date(value));
  const [status, setStatus] = reactExports.useState();
  const handleApiCall = async () => {
    setNotification(id, { message: "Optimizing…", status: "info" });
    const response = await getFetch(value);
    const message = await response.json();
    if (response.ok) {
      setNotification(id, { message, status: "success" });
      return false;
    }
    setNotification(id, { message, status: "error" });
  };
  const handleChange = useMutation({
    mutationFn: async (changeValue) => {
      setStatus("active");
      setNotification(id, { message: `Changing setting ${title}…`, status: "info" });
      const response = await setSettings(`${settingId}/${id}`, {
        value: changeValue
      });
      return { response };
    },
    onSuccess: async ({ response }) => {
      const { ok } = response;
      if (ok) {
        queryClient.invalidateQueries(["settings", settingId]);
        setStatus("success");
        setNotification(id, { message: `Setting ${title} changed!`, status: "success" });
        return false;
      }
      setStatus("error");
      setNotification(id, { message: `Changing setting ${title} failed`, status: "error" });
    }
  });
  const processDate = () => {
    const thisDate = new Date(date);
    const currentDate = new Date(thisDate.getTime() - thisDate.getTimezoneOffset() * 6e4);
    return currentDate;
  };
  const handleDate = useMutation({
    mutationFn: async () => {
      setStatus("active");
      setNotification(id, { message: `Changing date for ${title}…`, status: "info" });
      const response = await setSettings(`${settingId}/${id}`, {
        value: processDate().toISOString().replace(/^(.+?)T(.+?)\..+$/g, "$1 $2")
      });
      return { response };
    },
    onSuccess: async ({ response }) => {
      const { ok } = response;
      if (ok) {
        setStatus("success");
        setNotification(id, { message: `Setting date for ${title} changed!`, status: "success" });
        queryClient.invalidateQueries(["settings", settingId]);
        return false;
      }
      setStatus("error");
      setNotification(id, { message: `Changing date for ${title} failed`, status: "error" });
    }
  });
  const renderOption = () => {
    switch (type) {
      case "text":
      case "password":
      case "number":
        return /* @__PURE__ */ React.createElement(
          InputField,
          {
            key: id,
            type,
            label: title,
            placeholder: placeholder && !value,
            defaultValue: value,
            onChange: (inputValue) => handleChange.mutate(inputValue)
          }
        );
      case "textarea":
        return /* @__PURE__ */ React.createElement(
          TextArea,
          {
            key: id,
            type,
            label: title,
            placeholder: placeholder && !value,
            defaultValue: value,
            onChange: (inputValue) => handleChange.mutate(inputValue)
          }
        );
      case "api_button":
        return /* @__PURE__ */ React.createElement(
          Button,
          {
            active: true,
            key: id,
            onClick: handleApiCall
          },
          title
        );
      case "checkbox":
        return /* @__PURE__ */ React.createElement(
          Switch,
          {
            className: "option flex",
            key: id,
            label: title,
            defaultValue: value,
            onChange: (inputValue) => handleChange.mutate(inputValue)
          }
        );
      case "datetime":
        return /* @__PURE__ */ React.createElement("div", { className: "urlslab-inputField-datetime" }, /* @__PURE__ */ React.createElement("div", { className: "urlslab-inputField-label" }, title), /* @__PURE__ */ React.createElement(
          Ht,
          {
            className: "urlslab-input xl",
            selected: date,
            key: id,
            dateFormat: "dd. MMMM yyyy, HH:mm",
            timeFormat: "HH:mm",
            showTimeSelect: true,
            onChange: (newDate) => {
              setDate(newDate);
              handleDate.mutate();
            }
          }
        ));
      case "listbox":
        return /* @__PURE__ */ React.createElement(SortMenu, { key: id, className: "wide", name: id, items: possible_values, defaultValue: value, autoClose: true, onChange: (selectedId) => handleChange.mutate(selectedId) }, title);
      case "multicheck":
        return /* @__PURE__ */ React.createElement(
          MultiSelectMenu,
          {
            className: "wide",
            items: possible_values,
            defaultValue: value,
            key: id,
            id,
            asTags: true,
            onChange: (selectedItems) => handleChange.mutate(selectedItems)
          },
          title
        );
    }
  };
  return /* @__PURE__ */ React.createElement("div", { className: "urlslab-settingsPanel-option" }, status !== "error" && renderOption(), status === "error" && renderOption(), /* @__PURE__ */ React.createElement("p", { className: "urlslab-settingsPanel-option__desc", dangerouslySetInnerHTML: { __html: parseURL(description) } }));
}
const _Settings = "";
function Settings({ className, settingId }) {
  const queryClient = useQueryClient();
  const handleClick = (event) => {
    var _a;
    document.querySelectorAll(".urlslab-settingsPanel-section").forEach((section) => section.classList.remove("active"));
    (_a = event.target) == null ? void 0 : _a.closest(".urlslab-settingsPanel-section").classList.add("active");
  };
  const { data, status } = useQuery({
    queryKey: ["settings", settingId],
    queryFn: () => fetchSettings(settingId),
    initialData: () => {
      if (settingId === "general") {
        return queryClient.getQueryData(["settings", "general"]);
      }
    },
    refetchOnWindowFocus: false
  });
  if (status === "loading") {
    return /* @__PURE__ */ React.createElement(Loader, null);
  }
  const settings = data ? Object.values(data) : [];
  return /* @__PURE__ */ React.createElement(React.Fragment, null, Object.values(settings).map((section) => {
    return section.options ? /* @__PURE__ */ React.createElement("section", { onClick: handleClick, className: `urlslab-settingsPanel-section ${className}`, key: section.id }, /* @__PURE__ */ React.createElement("div", { className: "urlslab-settingsPanel urlslab-panel flex-tablet-landscape" }, /* @__PURE__ */ React.createElement("div", { className: "urlslab-settingsPanel-desc" }, /* @__PURE__ */ React.createElement("h4", null, section.title), /* @__PURE__ */ React.createElement("p", null, section.description)), /* @__PURE__ */ React.createElement("div", { className: "urlslab-settingsPanel-options" }, Object.values(section.options).map((option) => {
      return /* @__PURE__ */ React.createElement(SettingsOption, { settingId, option, key: option.id });
    })))) : "";
  }));
}
export {
  Settings as default
};
